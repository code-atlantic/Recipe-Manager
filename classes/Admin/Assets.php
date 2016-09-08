<?php


namespace RCPM\Admin;

use RCPM;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class Assets {

	public static function init() {
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'scripts_styles' ) );
	}

	public static function scripts_styles( $hook ) {
		global $post_type;

		// Use minified libraries if SCRIPT_DEBUG is turned off
		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';


		wp_register_style( 'dashicons-picker', RCPM::$URL . 'assets/css/dashicons-picker' . $suffix . '.css', array( 'dashicons' ), '1.0', false );
		wp_register_script( 'dashicons-picker', RCPM::$URL . 'assets/js/vendor/dashicons-picker' . $suffix . '.js', array( 'jquery' ), '1.1', true );

		wp_register_style( 'rcpm-admin-general', RCPM::$URL . 'assets/css/admin-general' . $suffix . '.css', array( 'dashicons', 'dashicons-picker' ), '1.0', false );
		wp_register_script( 'rcpm-admin-general', RCPM::$URL . 'assets/js/admin-general' . $suffix . '.js', array( 'jquery', 'dashicons-picker' ), RCPM::$VER, true );

		if ( $hook == 'recipe_page_rcpm-settings' ) {
			wp_enqueue_style( 'dashicons-picker' );
			wp_enqueue_script( 'dashicons-picker' );
			wp_enqueue_style( 'rcpm-admin-general' );
			wp_enqueue_script( 'rcpm-admin-general' );
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );
		}

		if ( 'recipe' == $post_type && in_array( $hook, array( 'post.php', 'post-new.php' ) ) ) {

			add_action( 'print_media_templates', array( '\\RCPM\Admin\Recipes\Templates', 'admin_footer' ) );

			wp_enqueue_style( 'rcpm-recipe-editor', RCPM::$URL . 'assets/css/admin-recipe-editor' . $suffix . '.css', array( 'rcpm-admin-general' ), RCPM::$VER );
			wp_enqueue_script( 'rcpm-recipe-editor', RCPM::$URL . 'assets/js/admin-recipe-editor' . $suffix . '.js', array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-autocomplete', 'jquery-ui-spinner', 'rcpm-admin-general' ), RCPM::$VER );
			wp_localize_script( 'rcpm-recipe-editor', 'rcpm_recipe_editor_vars', apply_filters( 'rcpm_recipe_editor_script_vars', array(
				'ingredients'       => rcpm_get_all_ingredients( array( 'ID', 'post_title as value' ) ),
				'measurement_units' => array(
					'singular' => rcpm_measurement_units( 'singular' ),
					'plural'   => rcpm_measurement_units( 'plural' ),
				),
				'I10n'              => array(
					'phase'                => __( 'Phase', 'recipe-manager' ),
					'confirm_delete_phase' => __( 'Are you sure you want to delete this phase?', 'recipe-manager' ),
				),
			) ) );

		}

		if ( 'ingredient' == $post_type && in_array( $hook, array( 'post.php', 'post-new.php' ) ) ) {

			wp_enqueue_style( 'rcpm-ingredient-editor', RCPM::$URL . 'assets/css/admin-ingredient-editor' . $suffix . '.css', array( 'rcpm-admin-general' ), RCPM::$VER );
			wp_enqueue_script( 'rcpm-ingredient-editor', RCPM::$URL . 'assets/js/admin-ingredient-editor' . $suffix . '.js', array( 'jquery', 'jquery-ui-tooltip', 'rcpm-admin-general' ), RCPM::$VER );
			wp_localize_script( 'ingredient-editor', 'rcpm_ingredient_editor_vars', apply_filters( 'rcpm_ingredient_editor_script_vars', array(
				'I10n' => array(),
			) ) );

		}
	}

}
