<?php
/**
 * Front-end Actions
 */

// Exit if accessed directly
namespace RCPM;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Actions {

	public static function init() {
		add_action( 'init', array( __CLASS__, 'get_actions' ) );
		add_action( 'init', array( __CLASS__, 'post_actions' ) );
	}

	/**
	 * Hooks Recipe Manager actions, when present in the $_GET superglobal. Every rcpm_action
	 * present in $_GET is called using WordPress's do_action function. These
	 * functions are called on init.
	 *
	 * @since 1.0
	 * @return void
	 */
	public static function get_actions() {
		if ( isset( $_GET['rcpm_action'] ) ) {
			do_action( 'rcpm_' . $_GET['rcpm_action'], $_GET );
		}
	}

	/**
	 * Hooks Recipe Manager actions, when present in the $_POST superglobal. Every rcpm_action
	 * present in $_POST is called using WordPress's do_action function. These
	 * functions are called on init.
	 *
	 * @since 1.0
	 * @return void
	 */
	public static function post_actions() {
		if ( isset( $_POST['rcpm_action'] ) ) {
			do_action( 'rcpm_' . $_POST['rcpm_action'], $_POST );
		}
	}

}

