<?php
/**
 * Recipe Manager Core Functions
 *
 * General core functions available on both the front-end and admin.
 *
 * @author 		DanielIser
 * @category 	Core
 * @package 	RCPM/Functions
 * @version    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Get template part (for templates like the recipe-loop).
 *
 * @param mixed $slug
 * @param string $name (default: '')
 * @param bool $load
 *
 * @return string
 */
function rcpm_get_template_part( $slug, $name = '', $load = true ) {
	// Execute code for this part
	do_action( 'rcpm_get_template_part_' . $slug, $slug, $name );

	// Setup possible parts
	$templates = array();
	if ( isset( $name ) ) {
		$templates[] = $slug . '-' . $name . '.php';
	}
	$templates[] = $slug . '.php';

	// Allow template parts to be filtered
	$templates = apply_filters( 'rcpm_get_template_part', $templates, $slug, $name );

	// Return the part that is found
	return rcpm_locate_template( $templates, $load, false );

}

/**
 * Get other templates (e.g. product attributes) passing attributes and including the file.
 *
 * @param string $template_name
 * @param array $args (default: array())
 */
function rcpm_get_template( $template_name, $args = array() ) {
	if ( $args && is_array( $args ) ) {
		extract( $args );
	}

	$located = rcpm_locate_template( $template_name );

	if ( ! file_exists( $located ) ) {
		_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $located ), '1.0' );
		return;
	}

	// Allow 3rd party plugin filter template file from their plugin
	$located = apply_filters( 'rcpm_get_template', $located, $template_name, $args );

	do_action( 'rcpm_before_template_part', $template_name, $located, $args );

	include( $located );

	do_action( 'rcpm_after_template_part', $template_name, $located, $args );
}

/**
 * Locate a template and return the path for inclusion.
 *
 * This is the load order:
 *
 *        yourtheme        /    $template_path    /    $template_name
 *        yourtheme        /    $template_name
 *        $default_path    /    $template_name
 *
 * @param $template_names
 * @param bool $load
 * @param bool $require_once
 *
 * @return string
 */
function rcpm_locate_template( $template_names, $load = false, $require_once = true ) {
	// No file found yet
	$located = false;

	$template_name = '';

	// Try to find a template file
	foreach ( (array) $template_names as $template_name ) {

		// Continue if template is empty
		if ( empty( $template_name ) ) {
			continue;
		}

		// Trim off any slashes from the template name
		$template_name = ltrim( $template_name, '/' );

		// try locating this template file by looping through the template paths
		foreach ( rcpm_get_template_paths() as $template_path ) {

			if ( file_exists( $template_path . $template_name ) ) {
				$located = $template_path . $template_name;
				break;
			}
		}

		if ( $located ) {
			break;
		}
	}

	// Return what we found
	$located = apply_filters( 'rcpm_locate_template', $located, $template_name );

	if ( ( true == $load ) && ! empty( $located ) ) {
		load_template( $located, $require_once );
	}

	return $located;
}


/**
 * Returns a list of paths to check for template locations
 */
function rcpm_get_template_paths() {

	$template_dir = rcpm()->template_path();

	$file_paths = array(
		1   => trailingslashit( get_stylesheet_directory() ) . $template_dir,
		10  => trailingslashit( get_template_directory() ) . $template_dir,
		100 => rcpm()->plugin_path() . 'templates/'
	);

	$file_paths = apply_filters( 'rcpm_template_paths', $file_paths );

	// sort the file paths based on priority
	ksort( $file_paths, SORT_NUMERIC );

	return array_map( 'trailingslashit', $file_paths );
}


/**
 * Enables template debug mode
 */
function rcpm_template_debug_mode() {
	if ( ! defined( 'RCPM_TEMPLATE_DEBUG_MODE' ) ) {
		$status_options = get_option( 'rcpm_status_options', array() );
		if ( ! empty( $status_options['template_debug_mode'] ) && current_user_can( 'manage_options' ) ) {
			define( 'RCPM_TEMPLATE_DEBUG_MODE', true );
		} else {
			define( 'RCPM_TEMPLATE_DEBUG_MODE', false );
		}
	}
}
add_action( 'after_setup_theme', 'rcpm_template_debug_mode', 20 );
