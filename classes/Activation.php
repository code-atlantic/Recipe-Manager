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

		// check minimum wp version
		// save default options
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
}
