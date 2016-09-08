<?php

// Exit if accessed directly
namespace RCPM\Admin\Recipes;

use RCPM\Admin\Recipes\Templates;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Metaboxes {

	public static function init() {
		add_action( 'add_meta_boxes_recipe', array( __CLASS__, 'register' ) );
		add_action( 'edit_form_after_editor', array( __CLASS__, 'recipe_card' ) );
		add_action( 'save_post', array( __CLASS__, 'meta_save' ), 10, 2 );
	}

	public static function register() {
		add_meta_box( 'recipe-information', 'Recipe Information', array(
			__CLASS__,
			'recipe_information',
		), 'recipe', 'side', 'high' );
	}

	public static function recipe_information( $post ) {
		global $pagenow;

		if ( $pagenow != 'post-new.php' ) {
			foreach ( self::meta_fields( 'keys' ) as $field ) {
				$$field = get_post_meta( $post->ID, $field, true );
			}
		} else {
			extract( self::meta_fields() );
		}

		wp_nonce_field( basename( __FILE__ ), 'rcpm_recipe_meta_box_nonce' );

		do_action( 'rcpm_recipe_info_metabox_before', $post ); ?>

		<div class=" recipe-times-fields">

			<p class="one-third">
				<label for="prep_time"><?php _e( 'Prep Time', 'recipe-manager' ); ?></label><br />
				<input type="text" name="prep_time" id="prep_time" class="timespinner" value="<?php esc_attr_e( $prep_time ); ?>" />
			</p>

			<p class="one-third">
				<label for="cook_time"><?php _e( 'Cook Time', 'recipe-manager' ); ?></label><br />
				<input type="text" name="cook_time" id="cook_time" class="timespinner" value="<?php esc_attr_e( $cook_time ); ?>" />
			</p>

			<p class="one-third last">
				<label for="total_time"><?php _e( 'Total Time', 'recipe-manager' ); ?></label><br />
				<input type="text" name="total_time" id="total_time" class="timespinner" value="<?php esc_attr_e( $total_time ); ?>" />
			</p>

			<p class="description"><?php _e( 'Enter the prep & cook times. HH:MM', 'recipe-manager' ); ?></p>
		</div>

		<p class="one-third recipe-servings-field">
			<label for="servings"><?php _e( 'Servings', 'recipe-manager' ); ?></label><br />
			<input type="number" name="servings" id="servings" value="<?php esc_attr_e( $servings ); ?>" />
		</p>

		<div class="clearfix"></div>

		<?php


		do_action( 'rcpm_recipe_info_metabox_after', $post );
	}

	public static function recipe_card( $post ) {

		if ( 'recipe' != get_post_type( $post ) ) {
			return;
		}

		$phases = get_post_meta( $post->ID, 'phases', true );

		if ( empty( $phases ) ) {
			$phases = array(
				array(),
			);
		} else {
			foreach ( $phases as $index => $phase ) {
				$phases[ $index ] = array_merge( $phase, get_post_meta( $post->ID, 'phase_' . $phase['key'], true ) );
			}
		}

		?>
		<div id="rcpm_recipe_card_metabox" class="rcpm_meta_table_wrap">

		<h2>
			<span><?php _e( 'Recipe Card', 'recipe-manager' ); ?></span>
			<span class="add-phase dashicons dashicons-plus-alt" title="<?php _e( 'Add Phase', 'recipe-manager' ); ?>"></span>
		</h2>

		<div class="phases"><?php
			foreach ( $phases as $phase_index => $phase ) :
				$phase['index']  = $phase_index;
				$phase['number'] = $phase_index + 1;
				Templates::phase( $phase );
			endforeach; ?>
		</div>			<!--end .phases-->

		<div class="additional-notes">
			<div class="metabox-title">
				<span class="title"><?php _e( 'Additional Notes', 'recipe-manager' ); ?></span>
			</div>
			<div class="metabox-content">
				<textarea style="width: 100%; display: block;" placeholder="<?php _e( 'Additional Notes', 'recipe-manager' ); ?>" name="additional_notes"><?php esc_html_e( get_post_meta( $post->ID, 'additional_notes', true ) ); ?></textarea>
			</div>
		</div>
		<?php do_action( 'rcpm_recipe_card_metabox', $post->ID ); ?>
		</div><?php
	}

	public static function meta_save( $post_id, $post ) {
		global $wpdb;

		if ( ! isset( $_POST['rcpm_recipe_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['rcpm_recipe_meta_box_nonce'], basename( __FILE__ ) ) ) {
			return;
		}

		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || isset( $_REQUEST['bulk_edit'] ) ) {
			return;
		}

		if ( isset( $post->post_type ) && 'revision' == $post->post_type ) {
			return;
		}

		// If this isn't a 'recipe' post, don't update it.
		if ( 'recipe' != $post->post_type ) {
			return;
		}

		foreach ( self::meta_fields( 'keys' ) as $field ) {

			if ( ! empty( $_POST[ $field ] ) ) {
				$new = apply_filters( 'rcpm_metabox_save_' . $field, $_POST[ $field ] );
				update_post_meta( $post_id, $field, $new );
			} else {
				delete_post_meta( $post_id, $field );
			}

		}

		$phases = get_post_meta( $post->ID, 'phases', true );

		if ( ! empty( $phases ) ) {
			foreach ( $phases as $index => $phase ) {
				delete_post_meta( $post->ID, 'phase_' . $phase['key'] );
			}
		}

		delete_post_meta( $post->ID, 'phases' );

		rcpm()->partner_tables->recipe_ingredients->delete_by( 'recipe_id', $post->ID );

		if ( ! empty( $_POST['recipe_card'] ) ) {

			$phases = array();

			$ingredient_ids = array();

			$ingredient_check = array();
			foreach ( rcpm_get_all_ingredients( array( 'ID', 'post_title' ) ) as $row ) {
				$ingredient_check[ $row->post_title ] = $row->ID;
			}

			foreach ( $_POST['recipe_card'] as $index => $card ) {

				$phase    = array(
					'name'        => $card['name'],
					'description' => $card['description'],
					'key'         => ! empty( $card['key'] ) ? $card['key'] : uniqid(),
				);
				$phases[] = $phase;

				if ( empty( $card['ingredients'] ) ) {
					$card['ingredients'] = array();
				}

				foreach ( $card['ingredients'] as $key => $ingredient ) {
					if ( empty( $ingredient['label'] ) ) {
						unset( $card['ingredients'][ $key ] );
						continue;
					}

					// Trim the extra whitespaces.
					$ingredient = array_map( 'trim', $ingredient );

					// Ingredient doesn't exist.
					if ( ! isset( $ingredient_check[ $ingredient['label'] ] ) ) {
						$ingredient['ID']                         = wp_insert_post( array(
							'post_title'  => $ingredient['label'],
							'post_type'   => 'ingredient',
							'post_status' => 'publish',
						) );
						$ingredient_check[ $ingredient['label'] ] = $ingredient['ID'];
					} // Ingredient IDs don't match.
					elseif ( $ingredient_check[ $ingredient['label'] ] != $ingredient['ID'] ) {
						$ingredient['ID'] = $ingredient_check[ $ingredient['label'] ];
					}
					$ingredient_ids[ $ingredient['ID'] ] = $ingredient['ID'];
					$card['ingredients'][ $key ]         = $ingredient;
				}

				if ( empty( $card['steps'] ) ) {
					$card['steps'] = array();
				}

				foreach ( $card['steps'] as $key => $step ) {
					if ( empty( $step['description'] ) ) {
						unset( $card['steps'][ $key ] );
						continue;
					}

					// Trim the extra whitespaces.
					$card['steps'][ $key ] = array_map( 'trim', $step );
				}

				$phase_meta = array(
					'ingredients' => $card['ingredients'],
					'steps'       => $card['steps'],
				);

				add_post_meta( $post->ID, 'phase_' . $phase['key'], $phase_meta, true );
			}

			update_post_meta( $post->ID, 'phases', $phases );

			rcpm()->partner_tables->recipe_ingredients->insert_ingredient_list( $post->ID, $ingredient_ids );

		}

	}

	public static function meta_fields( $return = null, $key = null ) {
		$fields = apply_filters( 'rcpm_recipe_meta_fields', array(
			'servings'         => '',
			'cook_time'        => '00:00',
			'prep_time'        => '00:00',
			'total_time'       => '00:00',
			'additional_notes' => '',
		) );

		if ( ! $return ) {
			return $fields;
		}
		switch ( $return ) {
			case 'keys':
				return array_keys( $fields );
				break;
			case 'default':
				return $key && isset( $fields[ $key ] ) ? $fields[ $key ] : null;
				break;
		}
	}

	public static function settings() {
		$settings = array(
			'recipe_information' => array(

				'servings' => array(
					'type'  => 'number',
					'std'   => '',
					'label' => __( 'Servings:', 'recipe-manager' ),
					//'options' => self::get_listing_taxonomies()
				),
			),

		);

		return apply_filters( 'rcpm_recipe_metabox_settings', $settings );
	}

}

