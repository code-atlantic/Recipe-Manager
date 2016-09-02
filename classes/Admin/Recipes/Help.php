<?php

// Exit if accessed directly
namespace RCPM\Admin\Recipes;

use RCPM\Post_Types;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Help {

	public static function init() {
		add_action( 'load-post.php', array( __CLASS__, 'contextual_help' ) );
		add_action( 'load-post-new.php', array( __CLASS__, 'contextual_help' ) );
	}

	public static function contextual_help() {
		$screen = get_current_screen();
		if ( $screen->id != 'recipe' ) {
			return;
		}

		$screen->set_help_sidebar( '<p><strong>' . sprintf( __( 'For more information:', 'recipe-manager' ) . '</strong></p>' . '<p>' . sprintf( __( 'Visit the <a href="%s">documentation</a> on the Recipe Manager website.', 'recipe-manager' ), esc_url( 'https://wprecipemanager.com/documentation/' ) ) ) . '</p>' . '<p>' . sprintf( __( '<a href="%s">Post an issue</a> on <a href="%s">GitHub</a>. View <a href="%s">extensions</a> or <a href="%s">themes</a>.', 'recipe-manager' ), esc_url( 'https://github.com/danieliser/Recipe-Manager/issues' ), esc_url( 'https://github.com/danieliser/Recipe-Manager' ), esc_url( 'https://wprecipemanager.com/addons/' ), esc_url( 'https://wprecipemanager.com/themes/' ) ) . '</p>' );

		$screen->add_help_tab( array(
			'id'      => 'rcpm-recipe-shortcuts',
			'title'   => sprintf( __( '%s Settings', 'recipe-manager' ), Post_Types::get_singular_label( 'recipe' ) ),
			'content' => '<p>' . __( '<strong>Phases</strong> - Define different parts of your recipe, such as Cake, Filling & Frosting.', 'recipe-manager' ) . '</p>',
		) );


		/**
		 * Fires off in the Recipe Manager Recipe Contextual Help Screen
		 *
		 * @since 1.2.3
		 *
		 * @param object $screen The current admin screen
		 */
		do_action( 'rcpm_recipe_contextual_help', $screen );
	}

}
