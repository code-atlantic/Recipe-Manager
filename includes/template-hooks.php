<?php
/**
 * Recipe Manager Template Hooks
 *
 * Action/filter hooks used for Recipe Manager functions/templates
 *
 * @author        Daniel Iser
 * @category    Core
 * @package    Recipe Manager/Templates
 * @version    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


add_filter( 'body_class', 'rcpm_body_class' );
add_filter( 'post_class', 'rcpm_recipe_post_class', 20, 3 );


/**
 * WP Header
 *
 * @see  rcpm_recipes_rss_feed()
 * @see  rcpm_generator_tag()
 */
add_action( 'wp_head', 'rcpm_recipes_rss_feed' );
add_action( 'get_the_generator_html', 'rcpm_generator_tag', 10, 2 );
add_action( 'get_the_generator_xhtml', 'rcpm_generator_tag', 10, 2 );


/**
 * Content Wrappers
 *
 * @see rcpm_output_content_wrapper()
 * @see rcpm_output_content_wrapper_end()
 */
add_action( 'rcpm_before_main_content', 'rcpm_output_content_wrapper', 10 );
add_action( 'rcpm_after_main_content', 'rcpm_output_content_wrapper_end', 10 );


/**
 * Breadcrumbs
 *
 * @see rcpm_breadcrumb()
 */
//add_action( 'rcpm_before_main_content', 'rcpm_breadcrumb', 20, 0 );


/**
 * Sidebar
 *
 * @see rcpm_get_sidebar()
 */
add_action( 'rcpm_sidebar', 'rcpm_get_sidebar', 10 );


/**
 * Archive descriptions
 *
 * @see rcpm_taxonomy_archive_description()
 * @see rcpm_recipe_archive_description()
 */
//add_action( 'rcpm_archive_description', 'rcpm_taxonomy_archive_description', 10 );
//add_action( 'rcpm_archive_description', 'rcpm_recipe_archive_description', 10 );


/**
 * Recipes Loop
 *
 * @see rcpm_result_count()
 * @see rcpm_reset_loop()
 */
add_action( 'rcpm_before_recipes_loop', 'rcpm_result_count', 20 );


/**
 * Recipe Loop Items
 */
add_action( 'rcpm_before_recipes_loop_item_title', 'rcpm_template_loop_recipe_thumbnail', 10 );
add_action( 'rcpm_recipes_loop_item_title', 'rcpm_template_loop_recipe_title', 10 );
add_action( 'rcpm_after_recipes_loop_item', 'rcpm_template_loop_recipe_meta', 10 );
add_action( 'rcpm_after_recipes_loop_item', 'rcpm_template_loop_recipe_excerpt', 30 );
add_action( 'rcpm_recipes_loop_item_meta', 'rcpm_template_loop_recipe_time', 10 );
add_action( 'rcpm_recipes_loop_item_meta', 'rcpm_template_loop_recipe_allergens', 20 );

/**
 * Recipe List Items
 */
add_action( 'rcpm_recipes_list_item_title', 'rcpm_template_loop_recipe_title', 10 );


/**
 * Recipe Shortcode
 */
add_action( 'rcpm_shortcode_recipe_summary', 'rcpm_template_loop_recipe_title', 5 );
add_action( 'rcpm_shortcode_recipe_summary', 'rcpm_show_recipe_images', 20 );
add_action( 'rcpm_shortcode_recipe_summary', 'rcpm_template_single_actions_bar', 30 );
add_action( 'rcpm_shortcode_recipe_summary', 'rcpm_template_single_description', 40 );
add_action( 'rcpm_shortcode_recipe_summary', 'rcpm_template_single_meta', 50 );
add_action( 'rcpm_shortcode_recipe_summary', 'rcpm_template_single_recipe_times', 50 );
add_action( 'rcpm_shortcode_recipe_summary', 'rcpm_template_single_recipe_phases', 60 );
add_action( 'rcpm_shortcode_recipe_summary', 'rcpm_template_single_additional_notes', 70 );


/**
 * Single Recipe
 *
 * @see rcpm_template_single_title()
 * @see rcpm_show_recipe_images()
 * @see rcpm_template_single_actions_bar()
 * @see rcpm_template_single_description()
 * @see rcpm_template_single_meta()
 */
add_action( 'rcpm_single_recipe_summary', 'rcpm_template_single_title', 10 );
add_action( 'rcpm_single_recipe_summary', 'rcpm_show_recipe_images', rcpm_get_option( 'featured_image_position', 20 ) );
add_action( 'rcpm_single_recipe_summary', 'rcpm_template_single_actions_bar', 30 );
add_action( 'rcpm_single_recipe_summary', 'rcpm_template_single_description', 40 );
add_action( 'rcpm_single_recipe_summary', 'rcpm_template_single_meta', 50 );
add_action( 'rcpm_single_recipe_summary', 'rcpm_template_single_recipe_times', 60 );
add_action( 'rcpm_single_recipe_summary', 'rcpm_recipe_ingredients', 70 );
add_action( 'rcpm_single_recipe_summary', 'rcpm_template_single_recipe_phases', 80 );
add_action( 'rcpm_single_recipe_summary', 'rcpm_template_single_additional_notes', 90 );
add_action( 'rcpm_single_recipe_summary', 'rcpm_template_single_allergen_warnings', 100 );
add_action( 'rcpm_single_recipe_summary', 'rcpm_template_single_meta_categories', 110 );
add_action( 'rcpm_single_recipe_summary', 'rcpm_template_single_meta_post_tags', 120 );


/**
 * Single Recipe Meta
 *
 * @see rcpm_template_single_title()
 */
add_action( 'rcpm_single_recipe_meta', 'rcpm_template_single_meta_courses', 10 );
add_action( 'rcpm_single_recipe_meta', 'rcpm_template_single_recipe_servings', 20 );
