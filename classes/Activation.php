<?php

namespace RCPM;

use RCPM;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Activation {

	public static function activate( $network_wide ) {
		self::do_multisite( $network_wide, array( __CLASS__, 'activate_plugin' ) );
	}

	public static function deactivate( $network_wide ) {
		self::do_multisite( $network_wide, array( __CLASS__, 'deactivate_plugin' ) );
	}

	public static function uninstall() {
		self::do_multisite( true, array( __CLASS__, 'uninstall_plugin' ) );
	}

	private static function do_multisite( $network_wide, $method, $args = array() ) {
		global $wpdb;

		if ( is_multisite() && $network_wide ) {

			$blog_ids = $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs}" );

			foreach ( $blog_ids as $blog_id ) {
				switch_to_blog( $blog_id );
				call_user_func_array( $method, array( $args ) );
				restore_current_blog();
			}

		} else {
			call_user_func_array( $method, array( $args ) );
		}
	}

	private static function activate_plugin() {
		global $wp_rewrite;
		Post_Types::initialize();
		Partner_Tables\Recipe_Ingredients::activation_install( false );

		// Flush rewrite rules in a multisite compatible way.
		set_transient( 'rcpm_flush_rewrite_rules', true );

		/**
		 * Install default recipe taxonomy terms.
		 */
		if ( ! get_option( 'rcpm_default_recipe_course_terms', false ) ) {
			$default_tax_terms = array(
				'recipe_course' => array(
					__( 'Appetizer', 'recipe-manager' ),
					__( 'Brunch', 'recipe-manager' ),
					__( 'Breakfast', 'recipe-manager' ),
					__( 'Lunch', 'recipe-manager' ),
					__( 'Dinner', 'recipe-manager' ),
					__( 'Dessert', 'recipe-manager' ),
					__( 'Beverage', 'recipe-manager' ),
				)
			);

			foreach ( $default_tax_terms as $tax => $terms ) {
				foreach ( $terms as $term ) {
					wp_insert_term( $term, $tax );
				}
			}

			add_option( 'rcpm_default_recipe_course_terms', true );
		}

		/**
		 * Install allergenic ingredients.
		 */
		if ( ! get_option( 'rcpm_default_allergen_ingredients', false ) ) {
			$default_allergens = array(
				__( 'Peanut', 'recipe-manager'),
				__( 'Tree nuts', 'recipe-manager'),
				__( 'Milk', 'recipe-manager'),
				__( 'Egg', 'recipe-manager'),
				__( 'Wheat', 'recipe-manager'),
				__( 'Soy', 'recipe-manager'),
				__( 'Fish', 'recipe-manager'),
				__( 'Shellfish', 'recipe-manager'),
			);

			foreach ( $default_allergens as $ingredient ) {
				wp_insert_post( array(
					'post_type' => 'ingredient',
					'post_title' => $ingredient,
					'post_status' => 'publish',
					'meta_input' => array(
						'_is_allergen' => true,
						'_has_lactose' => $ingredient == __( 'Milk', 'recipe-manager')
					)
				) );
			}

			add_option( 'rcpm_default_allergen_ingredients', true );
		}

		/**
		 * Install default recipe & ingredients.
		 */
		if ( ! get_option( 'rcpm_sample_recipe_1', false ) ) {

			$ingredient_ids = array();

			$sample_ingredients = array(
				__( 'Sunflower Oil', 'recipe-manager' ),
				__( 'Baby New Potatoes', 'recipe-manager' ),
				__( 'Leg of Lamb', 'recipe-manager' ),
				__( 'Garlic Bulb', 'recipe-manager' ),
				__( 'Carrots', 'recipe-manager' ),
				__( 'Rosemary', 'recipe-manager' ),
				__( 'Fresh Lemon Juice', 'recipe-manager' ),
			);

			foreach( $sample_ingredients as $ingredient ) {
				$ingredient_id = wp_insert_post( array( 'post_type' => 'ingredient', 'post_title' => $ingredient, 'post_status' => 'publish' ) );
				$ingredient_ids[ $ingredient ] = ! is_wp_error( $ingredient_id ) ? $ingredient_id : null;
			}

			$recipe = array(
				'post_title'    => __( 'Slow Roasted Lamb with Fried Potatoes', 'recipe-manager' ),
				'post_content'  => __( 'This is the ideal meal for a special occasion or lazy sunday afternoon.', 'recipe-manager' ),
				'post_name'     => __( 'slow-roasted-lamb-with-fried-potatoes', 'recipe-manager' ),
				'post_status'   => 'publish',
				'post_type'     => 'recipe',
				'meta_input'    => array(
					'phases' => array(
						array(
							'name'        => __( 'The Lamb', 'recipe-manager' ),
							'description' => __( 'The most important thing to remember about cooking a leg of lamb is to not over-cook it. Lamb has such fantastic flavor on its own, and is so naturally tender, that it is bound to turn out well, as long as it still has a touch of pink to the meat.', 'recipe-manager' ),
							'key'         => '57cf3b9ad1639',
						),
						array(
							'name'        => __( 'The Potatoes', 'recipe-manager' ),
							'description' => __( 'A wonderful alternative to chips and so much simpler to prepare', 'recipe-manager' ),
							'key'         => '57cf3e85ada1d',
						),
					),
					'phase_57cf3b9ad1639' => array (
						'ingredients' => array(
							array(
								'measure' => '1',
								'unit'    => '',
								'ID'      => $ingredient_ids[ __( 'Leg of Lamb', 'recipe-maker' ) ],
								'label'   => __( 'Leg of Lamb', 'recipe-maker' ),
								'note'    => '',
							),
							array(
								'measure' => '1',
								'unit'    => '',
								'ID'      => $ingredient_ids[ __( 'Leg of Lamb', 'recipe-maker' ) ],
								'label'   => __( 'Garlic Bulb', 'recipe-maker' ),
								'note'    => '',
							),
							array(
								'measure' => '5',
								'unit'    => '',
								'ID'      => $ingredient_ids[ __( 'Leg of Lamb', 'recipe-maker' ) ],
								'label'   => __( 'Carrots', 'recipe-maker' ),
								'note'    => __( 'Cut into quarters', 'recipe-maker' ),
							),
							array(
								'measure' => '4',
								'unit'    => 'tbsp',
								'ID'      => $ingredient_ids[ __( 'Rosemary', 'recipe-maker' ) ],
								'label'   => __( 'Rosemary', 'recipe-maker' ),
								'note'    => __( 'Chopped', 'recipe-maker' ),
							),
							array(
								'measure' => '1/4',
								'unit'    => 'cup',
								'ID'      => $ingredient_ids[ __( 'Fresh Lemon Juice', 'recipe-maker' ) ],
								'label'   => __( 'Fresh Lemon Juice', 'recipe-maker' ),
								'note'    => '',
							),
						),
						'steps'       => array(
							array(
								'description' => __( 'First, stud the lamb with rosemary. Use a sharp pointed knife, make at least 20 incisions all over the meat. Peel 4 garlic cloves, crush them them and spread evenly over the lamb. Next, push the rosemary into the incisions.', 'recipe-maker' ),
								'note'        => '',
							),
							array(
								'description' => __( 'Heat oven to 190C/170C fan/gas 5. Heat a large frying pan, add the oil and brown the lamb all over. Scatter the carrot, remaining garlic and rosemary in a large roasting tin.', 'recipe-maker' ),
								'note'        => '',
							),
							array(
								'description' => __( 'Roast for about 1 hr 45 mins. When cooked, remove the lamb and allow to rest in a warm place covered in foil for about 30 mins.', 'recipe-maker' ),
								'note'        => '',
							),
						),
					),
					'phase_57cf3e85ada1d' => array(
						'ingredients' => array(
							array(
								'measure' => '1',
								'unit'    => 'kilogram',
								'ID'      => $ingredient_ids[ __( 'Baby New Potatoes', 'recipe-manager' ) ],
								'label'   => __( 'Baby New Potatoes', 'recipe-manager' ),
								'note'    => '',
							),
							array(
								'measure' => '8',
								'unit'    => 'tbsp',
								'ID'      => $ingredient_ids[ __( 'Sunflower Oil', 'recipe-maker' ) ],
								'label'   => __( 'Sunflower Oil', 'recipe-manager' ),
								'note'    => '',
							),
						),
						'steps'       => array(
							array(
								'description' => __( 'Cut the potatoes into chunks. Bring a large pan of water to the boil, then cook the potatoes for 3 mins. Drain, shake out onto a kitchen paper-lined tray and leave to cool.', 'recipe-manager' ),
								'note'        => '',
							),
							array(
								'description' => __( 'When ready to serve, heat the oil in a large non-stick frying pan until you can feel a strong heat rising. If your pan isn’t large enough, fry the potatoes in two batches – rather than crowding them. Have kitchen paper ready to drain them on. Add the potatoes in a single layer, not too tightly packed. Turn the heat to medium-high, so that the potatoes sizzle, but don’t stir until they start to brown underneath.', 'recipe-manager' ),
								'note'        => '',
							),
							array(
								'description' => __( 'Turn them all evenly 2 or 3 times until nicely browned all over – this can take about 7 mins. Then lift out with a fish slice or large slotted spoon to drain on more kitchen paper. Sprinkle with sea salt.', 'recipe-manager' ),
								'note'        => '',
							),
						),
					),
					'additional_notes' => __( 'Aluminum foil can be used to keep food moist, cook it evenly, and make clean-up easier.', 'recipe-manager' ),
					'servings' => 6,
					'cook_time' => '01:45',
					'prep_time' => '00:30',
					'total_time' => '02:15',
				),
			);

			$recipe_id = wp_insert_post( $recipe );

			wp_set_object_terms( $recipe_id, array( __( 'Dinner', 'recipe-manager' ) ), 'recipe_course', false );

			// Download and set the featured image. Licensed CC0 & GPL Compatible.
			$image_id = static::_import_photo( $recipe_id, 'https://hd.unsplash.com/photo-1457556675483-024ebaa3adb7' );
			add_post_meta( $recipe_id, '_thumbnail_id', $image_id, true );

			$ingredient_ids = array_values( $ingredient_ids );

			$recipe_ingredients = new Partner_Tables\Recipe_Ingredients;
			$recipe_ingredients->insert_ingredient_list( $recipe_id, array_values( $ingredient_ids ) );

			add_option( 'rcpm_sample_recipe_1', true );

			//$redirect = admin_url( 'post.php?post=' . $recipe_id . '&action=edit' );
		}


		if ( isset( $redirect ) ) {
			add_option( 'rcpm_activation_redirect', $redirect );
		}
	}

	private static function deactivate_plugin() {
		flush_rewrite_rules();
		// clear scheduled cron jobs / hooks
	}

	private static function uninstall_plugin() {
		// remove settings from the options table
		// remove user options, including metabox preferences
		// remove post meta options
		// remove transients
	}

	public static function _import_photo( $postid, $image_url ) {
		$post = get_post( $postid );
		if ( empty( $post ) ) {
			return false;
		}

		if ( ! class_exists( '\WP_Http' ) ) {
			include_once( ABSPATH . WPINC . '/class-http.php' );
		}

		$photo = new \WP_Http;
		$photo = $photo->request( $image_url );
		if ( $photo['response']['code'] != 200 ) {
			return false;
		}

		$attachment = wp_upload_bits( $post->post_name . '.jpg', null, $photo['body'], date( "Y-m", strtotime( $photo['headers']['last-modified'] ) ) );
		if ( ! empty( $attachment['error'] ) ) {
			return false;
		}

		$filetype = wp_check_filetype( basename( $attachment['file'] ), null );

		$postinfo  = array(
			'post_mime_type' => $filetype['type'],
			'post_title'     => $post->post_title . ' employee photograph',
			'post_content'   => '',
			'post_status'    => 'inherit',
		);
		$filename  = $attachment['file'];
		$attach_id = wp_insert_attachment( $postinfo, $filename, $postid );

		if ( ! function_exists( 'wp_generate_attachment_data' ) ) {
			require_once( ABSPATH . "wp-admin" . '/includes/image.php' );
		}
		$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
		wp_update_attachment_metadata( $attach_id, $attach_data );

		return $attach_id;
	}
}
