<?php

// Exit if accessed directly
namespace RCPM\Admin\Ingredients;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Metaboxes {

	public static function init() {
		add_action( 'add_meta_boxes_ingredient', array( __CLASS__, 'register' ) );
		add_action( 'rcpm_save_ingredient', array( __CLASS__, 'save_ingredient_data' ), 20, 2 );
		add_action( 'save_post', array( __CLASS__, 'save_post' ), 10, 2 );
	}

	public static function register() {
		add_meta_box( 'ingredient-information', 'Ingredient Information', array(
			__CLASS__,
			'ingredient_data',
		), 'ingredient', 'normal', 'high' );
	}


	/**
	 * ingredient_fields function.
	 *
	 * @access public
	 * @return array $fields
	 */
	public static function ingredient_fields() {
		$fields = array(
			'_is_allergen'      => array(
				'label'       => __( 'Is Allergenic', 'recipe-manager' ),
				'description' => __( 'Is the ingredient an allergen?', 'recipe-manager' ),
				'type'        => 'checkbox',
				'priority'    => 1,
			),
			'_allergen_warning' => array(
				'label'       => __( 'Allergen Warning Note', 'recipe-manager' ),
				'description' => __( 'This will be display on recipes that include this ingredient. Leave blank to use the default.', 'recipe-manager' ),
				'priority'    => 1,
			),
			'_has_lactose'      => array(
				'label'       => __( 'Contains Dairy', 'recipe-manager' ),
				'description' => __( 'Does this contain lactose?', 'recipe-manager' ),
				'type'        => 'checkbox',
				'priority'    => 2,
			),
		);

		$fields = apply_filters( 'rcpm_ingredient_data_fields', $fields );

		uasort( $fields, array( __CLASS__, 'sort_by_priority' ) );

		return $fields;
	}

	/**
	 * Sort array by priority value
	 */
	public static function sort_by_priority( $a, $b ) {
		if ( ! isset( $a['priority'] ) || ! isset( $b['priority'] ) || $a['priority'] === $b['priority'] ) {
			return 0;
		}

		return ( $a['priority'] < $b['priority'] ) ? - 1 : 1;
	}

	/**
	 * input_file function.
	 *
	 * @param mixed $key
	 * @param mixed $field
	 */
	public static function input_file( $key, $field ) {
		global $thepostid;

		$field = wp_parse_args( $field, array(
			'label'       => '',
			'description' => '',
			'value'       => get_post_meta( $thepostid, $key, true ),
			'multiple'    => false,
			'placeholder' => 'http://',
			'name'        => $key,
		) ); ?>

		<p class="form-field <?php echo esc_attr( $key ); ?>">

			<label for="<?php echo esc_attr( $key ); ?>">

				<?php echo esc_html( $field['label'] ); ?>:

				<?php if ( ! empty( $field['description'] ) ) : ?>
					<span class="tips" data-tip="<?php echo esc_attr( $field['description'] ); ?>">[?]</span>
				<?php endif; ?>

			</label>

			<?php if ( ! empty( $field['multiple'] ) ) : ?>

				<?php foreach ( (array) $field['value'] as $value ) : ?>

					<span class="file_url">

						<input type="text" name="<?php echo esc_attr( $field['name'] ); ?>[]" placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>" value="<?php echo esc_attr( $value ); ?>" />

						<button class="button button-small rcpm_upload_file_button" data-uploader_button_text="<?php _e( 'Use file', 'recipe-manager' ); ?>">
							<?php _e( 'Upload', 'recipe-manager' ); ?>
						</button>

					</span>

				<?php endforeach; ?>

				<button class="button button-small rcpm_add_another_file_button" data-field_name="<?php echo esc_attr( $key ); ?>" data-field_placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>" data-uploader_button_text="<?php _e( 'Use file', 'recipe-manager' ); ?>" data-uploader_button="<?php _e( 'Upload', 'recipe-manager' ); ?>">
					<?php _e( 'Add file', 'recipe-manager' ); ?>
				</button>

			<?php else : ?>

				<span class="file_url">

					<input type="text" name="<?php echo esc_attr( $field['name'] ); ?>" id="<?php echo esc_attr( $key ); ?>" placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>" value="<?php echo esc_attr( $field['value'] ); ?>" />

					<button class="button button-small rcpm_upload_file_button" data-uploader_button_text="<?php _e( 'Use file', 'recipe-manager' ); ?>">
						<?php _e( 'Upload', 'recipe-manager' ); ?>
					</button>

				</span>

			<?php endif ?>

		</p>
		<?php
	}

	/**
	 * input_text function.
	 *
	 * @param mixed $key
	 * @param mixed $field
	 */
	public static function input_text( $key, $field ) {
		global $thepostid;

		$field = wp_parse_args( $field, array(
			'label'       => '',
			'description' => '',
			'value'       => get_post_meta( $thepostid, $key, true ),
			'placeholder' => '',
			'name'        => $key,
		) ); ?>
	<p class="form-field <?php echo esc_attr( $key ); ?>">
		<label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field['label'] ); ?>: <?php if ( ! empty( $field['description'] ) ) : ?>
			<span class="tips" data-tip="<?php echo esc_attr( $field['description'] ); ?>">[?]</span><?php endif; ?>
		</label>
		<input type="text" name="<?php echo esc_attr( $field['name'] ); ?>" id="<?php echo esc_attr( $key ); ?>" placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>" value="<?php echo esc_attr( $field['value'] ); ?>" />
		</p><?php
	}

	/**
	 * input_text function.
	 *
	 * @param mixed $key
	 * @param mixed $field
	 */
	public static function input_textarea( $key, $field ) {
		global $thepostid;

		$field = wp_parse_args( $field, array(
			'label'       => '',
			'description' => '',
			'value'       => get_post_meta( $thepostid, $key, true ),
			'placeholder' => '',
			'name'        => $key,
		) ); ?>
	<p class="form-field <?php echo esc_attr( $key ); ?>">
		<label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field['label'] ); ?>: <?php if ( ! empty( $field['description'] ) ) : ?>
			<span class="tips" data-tip="<?php echo esc_attr( $field['description'] ); ?>">[?]</span><?php endif; ?>
		</label>
		<textarea name="<?php echo esc_attr( $field['name'] ); ?>" id="<?php echo esc_attr( $key ); ?>" placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>"><?php echo esc_html( $field['value'] ); ?></textarea>
		</p><?php
	}

	/**
	 * input_select function.
	 *
	 * @param mixed $key
	 * @param mixed $field
	 */
	public static function input_select( $key, $field ) {
		global $thepostid;

		$field = wp_parse_args( $field, array(
			'label'       => '',
			'description' => '',
			'value'       => get_post_meta( $thepostid, $key, true ),
			'name'        => $key,
			'options'     => array(),
		) ); ?>
	<p class="form-field <?php echo esc_attr( $key ); ?>">
		<label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field['label'] ); ?>: <?php if ( ! empty( $field['description'] ) ) : ?>
			<span class="tips" data-tip="<?php echo esc_attr( $field['description'] ); ?>">[?]</span><?php endif; ?>
		</label>
		<select name="<?php echo esc_attr( $field['name'] ); ?>" id="<?php echo esc_attr( $key ); ?>">
			<?php foreach ( $field['options'] as $value => $option ) : ?>
				<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $field['value'], $value ); ?>><?php echo esc_html( $option ); ?></option>
			<?php endforeach; ?>
		</select>
		</p><?php
	}

	/**
	 * input_select function.
	 *
	 * @param mixed $key
	 * @param mixed $field
	 */
	public static function input_multiselect( $key, $field ) {
		global $thepostid;

		$field = wp_parse_args( $field, array(
			'label'       => '',
			'description' => '',
			'value'       => get_post_meta( $thepostid, $key, true ),
			'name'        => $key,
			'options'     => array(),
		) ); ?>
	<p class="form-field <?php echo esc_attr( $key ); ?>">
		<label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field['label'] ); ?>: <?php if ( ! empty( $field['description'] ) ) : ?>
			<span class="tips" data-tip="<?php echo esc_attr( $field['description'] ); ?>">[?]</span><?php endif; ?>
		</label>
		<select multiple="multiple" name="<?php echo esc_attr( $field['name'] ); ?>[]" id="<?php echo esc_attr( $key ); ?>">
			<?php foreach ( $field['options'] as $value => $option ) : ?>
				<option value="<?php echo esc_attr( $value ); ?>" <?php if ( ! empty( $field['value'] ) && is_array( $field['value'] ) ) {
					selected( in_array( $value, $field['value'] ), true );
				} ?>><?php echo esc_html( $option ); ?></option>
			<?php endforeach; ?>
		</select>
		</p><?php
	}

	/**
	 * input_checkbox function.
	 *
	 * @param mixed $key
	 * @param mixed $field
	 */
	public static function input_checkbox( $key, $field ) {
		global $thepostid;

		$field = wp_parse_args( $field, array(
			'label'       => '',
			'description' => '',
			'value'       => get_post_meta( $thepostid, $key, true ),
			'name'        => $key,
		) ); ?>
		<p class="form-field form-field-checkbox <?php echo esc_attr( $key ); ?>">
			<label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
			<input type="checkbox" class="checkbox" name="<?php echo esc_attr( $field['name'] ); ?>" id="<?php echo esc_attr( $key ); ?>" value="1" <?php checked( $field['value'], 1 ); ?> />
			<?php if ( ! empty( $field['description'] ) ) : ?>
			<span class="tips" data-tip="<?php echo esc_attr( $field['description'] ); ?>">[?]</span><?php endif; ?>
		</p>
		<?php
	}

	/**
	 * input_radio function.
	 *
	 * @param mixed $key
	 * @param mixed $field
	 */
	public static function input_radio( $key, $field ) {
		global $thepostid;

		$field = wp_parse_args( $field, array(
			'label'       => '',
			'description' => '',
			'value'       => get_post_meta( $thepostid, $key, true ),
			'name'        => $key,
			'options'     => array(),
		) ); ?>
	<p class="form-field form-field-radio <?php echo esc_attr( $key ); ?>">
		<label><?php echo esc_html( $field['label'] ); ?></label>
		<?php foreach ( $field['options'] as $value => $option ) : ?>
			<label><input type="radio" class="radio" name="<?php echo esc_attr( $field['name'] ); ?>" value="<?php echo esc_attr( $value ); ?>" <?php checked( $field['value'], $value ); ?> /> <?php echo esc_html( $option ); ?>
			</label>
		<?php endforeach; ?><?php if ( ! empty( $field['description'] ) ) : ?>
			<span class="description"><?php echo $field['description']; ?></span><?php endif; ?>
		</p><?php
	}

	/**
	 * ingredient_data function.
	 *
	 * @access public
	 *
	 * @param mixed $post
	 *
	 * @return void
	 */
	public static function ingredient_data( $post ) {
		global $post, $thepostid;

		$thepostid = $post->ID;

		echo '<div class="rcpm_meta_data">';

		wp_nonce_field( 'save_meta_data', 'rcpm_nonce' );

		do_action( 'rcpm_ingredient_data_start', $thepostid );

		foreach ( self::ingredient_fields() as $key => $field ) {
			$type = ! empty( $field['type'] ) ? $field['type'] : 'text';

			if ( has_action( 'rcpm_input_' . $type ) ) {
				do_action( 'rcpm_input_' . $type, $key, $field );
			} elseif ( method_exists( __CLASS__, 'input_' . $type ) ) {
				call_user_func( array( __CLASS__, 'input_' . $type ), $key, $field );
			}
		}

		do_action( 'rcpm_ingredient_data_end', $thepostid );

		echo '</div>';
	}

	/**
	 * save_post function.
	 *
	 * @access public
	 *
	 * @param mixed $post_id
	 * @param mixed $post
	 *
	 * @return void
	 */
	public static function save_post( $post_id, $post ) {
		if ( empty( $post_id ) || empty( $post ) || empty( $_POST ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( is_int( wp_is_post_revision( $post ) ) ) {
			return;
		}
		if ( is_int( wp_is_post_autosave( $post ) ) ) {
			return;
		}
		if ( empty( $_POST['rcpm_nonce'] ) || ! wp_verify_nonce( $_POST['rcpm_nonce'], 'save_meta_data' ) ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		if ( $post->post_type != 'ingredient' ) {
			return;
		}

		do_action( 'rcpm_save_ingredient', $post_id, $post );
	}

	/**
	 * save_ingredient_data function.
	 *
	 * @access public
	 *
	 * @param mixed $post_id
	 * @param mixed $post
	 *
	 * @return void
	 */
	public static function save_ingredient_data( $post_id, $post ) {
		global $wpdb;

		// Save fields
		foreach ( self::ingredient_fields() as $key => $field ) {

			if ( has_action( 'save_ingredient_data_field_' . $key ) ) {
				do_action( 'save_ingredient_data_field_' . $key, $key, $field );
			} // Everything else
			else {
				$type = ! empty( $field['type'] ) ? $field['type'] : '';
				switch ( $type ) {
					case 'textarea' :
						update_post_meta( $post_id, $key, wp_kses_post( stripslashes( $_POST[ $key ] ) ) );
						break;
					case 'checkbox' :
						if ( isset( $_POST[ $key ] ) ) {
							update_post_meta( $post_id, $key, 1 );
						} else {
							update_post_meta( $post_id, $key, 0 );
						}
						break;
					default :
						if ( ! isset( $_POST[ $key ] ) ) {
							continue;
						} elseif ( is_array( $_POST[ $key ] ) ) {
							update_post_meta( $post_id, $key, array_filter( array_map( 'sanitize_text_field', $_POST[ $key ] ) ) );
						} else {
							update_post_meta( $post_id, $key, sanitize_text_field( $_POST[ $key ] ) );
						}
						break;
				}
			}
		}
	}

}
