<?php

// Exit if accessed directly
namespace RCPM;

use RCPM;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Site {

	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'scripts_styles' ), 0 );
		add_filter( 'template_include', array( '\\RCPM\Template', 'loader' ) );
	}

	public static function scripts_styles() {
		// Use minified libraries if SCRIPT_DEBUG is turned off
		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		wp_enqueue_script( 'rcpm-scripts', RCPM::$URL . 'assets/js/rcpm' . $suffix . '.js', array( 'jquery' ), RCPM::$VER, true );
		wp_enqueue_style( 'rcpm-styles', RCPM::$URL . 'assets/css/rcpm' . $suffix . '.css', null, RCPM::$VER );

		$theme = wp_get_theme()->get_stylesheet();



		if ( in_array( $theme, array( 'twentyeleven', 'twentyfourteen', 'twentyfifteen', 'twentysixteen' ) ) ) {
			wp_enqueue_style( 'rcpm-layout', RCPM::$URL . 'assets/css/rcpm-layout' . $suffix . '.css', null, RCPM::$VER );
		}

	}

}
