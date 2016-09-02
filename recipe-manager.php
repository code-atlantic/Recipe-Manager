<?php
/**
 * Plugin Name: Recipe Manager
 * Plugin URI: http://wprecipemanager.com
 * Description:
 * Author: danieliser
 * Version: 1.0.0
 * Author URI: http://danieliser.com
 * Text Domain: recipe-manager
 *
 * Minimum PHP: 5.3
 * Minimum WP: 3.6
 *
 * @author      Daniel Iser
 * @copyright   Copyright (c) 2016, Daniel Iser
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function rcpm_autoloader( $class ) {

	// project-specific namespace prefix
	$prefix = 'RCPM\\';

	// base directory for the namespace prefix
	$base_dir = __DIR__ . '/classes/';

	// does the class use the namespace prefix?
	$len = strlen( $prefix );
	if ( strncmp( $prefix, $class, $len ) !== 0 ) {
		// no, move to the next registered autoloader
		return;
	}

	// get the relative class name
	$relative_class = substr( $class, $len );

	// replace the namespace prefix with the base directory, replace namespace
	// separators with directory separators in the relative class name, append
	// with .php
	$file = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';

	// if the file exists, require it
	if ( file_exists( $file ) ) {
		require_once $file;
	}

}

spl_autoload_register( 'rcpm_autoloader' ); // Register autoloader


/**
 * Main class
 */
class RCPM {

	/**
	 * @var string
	 */
	public static $NAME = 'Recipe Manager';

	/**
	 * @var string
	 */
	public static $VER = '1.0.0';

	/**
	 * @var string
	 */
	public static $MIN_PHP_VER = '5.3';

	/**
	 * @var string
	 */
	public static $MIN_WP_VER = '3.6';

	/**
	 * @var string
	 */
	public static $URL = '';

	/**
	 * @var string
	 */
	public static $DIR = '';

	/**
	 * @var string
	 */
	public static $FILE = '';

	/**
	 * @var string
	 */
	public static $TEMPLATE_PATH = 'rcpm/';

	/**
	 * @var         RCPM $instance The one true RCPM
	 * @since       1.0.0
	 */
	private static $instance;

	public $partner_tables;

	/**
	 * Get active instance
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      object self::$instance The one true RCPM
	 */
	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new static;
			self::$instance->setup_constants();

			add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );

			self::$instance->includes();
			self::$instance->init();
			self::$instance->partner_tables = new \RCPM\Partner_Tables();

		}

		return self::$instance;
	}

	/**
	 * Setup plugin constants
	 *
	 * @access      private
	 * @since       1.0.0
	 * @return      void
	 */
	private function setup_constants() {
		RCPM::$DIR  = rcpm()->plugin_path();
		RCPM::$URL  = rcpm()->plugin_url();
		RCPM::$FILE = __FILE__;
	}

	/**
	 * Include necessary files, excluding classes which are auto loaded.
	 *
	 * The only thing that should eventually be here are template tags & global functions
	 *
	 * @access      private
	 * @since       1.0.0
	 * @return      void
	 */
	private function includes() {

		global $rcpm_options;
		// @todo move these into namespaced classes.
		require_once RCPM::$DIR . 'includes/setting-functions.php';
		$rcpm_options = rcpm_get_settings();

		// Template Tags
		// @todo Full Audit and reorganization. Find unused and remove.
		require_once RCPM::$DIR . 'includes/template-functions.php';
		// @todo Audit these for accuracy. Find any missing / broken / unneeded and remove.
		require_once RCPM::$DIR . 'includes/template-hooks.php';

		// Global Data Wrapper functions. Global access to Recipe & Ingredient objects.
		// @todo audit these to take better advantage of objects & caching.
		require_once RCPM::$DIR . 'includes/recipe-functions.php';
		require_once RCPM::$DIR . 'includes/ingredient-functions.php';

		// Schema & Microdata functions.
		// @todo Audit these, need to add the rest of the microdata formats.
		require_once RCPM::$DIR . 'includes/schema-functions.php';
		require_once RCPM::$DIR . 'includes/data-attr-functions.php';

		// General Usage Functions
		require_once RCPM::$DIR . 'includes/markup-functions.php';
		require_once RCPM::$DIR . 'includes/format-functions.php';
		require_once RCPM::$DIR . 'includes/templating-functions.php';

		// Admin functions
		// @todo move these into namespaced classes.
		require_once RCPM::$DIR . 'includes/addon-functions.php';
		require_once RCPM::$DIR . 'includes/tool-functions.php';

	}

	/**
	 * Initialize plugin.
	 */
	private function init() {

		\RCPM\Actions::init();
		\RCPM\Post_Types::init();
		\RCPM\Cron::init();
		\RCPM\Shortcodes::init();
		\RCPM\Site::init();
		\RCPM\Admin::init();
		\RCPM\Tracking::init();
	}

	/**
	 * Internationalization
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      void
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'recipe-manager' );
	}

	/**
	 * Get the plugin url.
	 * @return string
	 */
	public function plugin_url() {
		return plugins_url( '/', __FILE__ );
	}

	/**
	 * Get the plugin path.
	 * @return string
	 */
	public function plugin_path() {
		return plugin_dir_path( __FILE__ );
	}

	/**
	 * Get the template path.
	 * @return string
	 */
	public function template_path() {
		return apply_filters( 'rcpm_template_path', static::$TEMPLATE_PATH );
	}

	/**
	 * Get Ajax URL.
	 * @return string
	 */
	public function ajax_url() {
		return admin_url( 'admin-ajax.php', 'relative' );
	}

}

/**
 * The main function responsible for returning the one true RCPM
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $rcpm = RCPM(); ?>
 *
 * @since 1.0.0
 * @return object The one true RCPM Instance
 */
function rcpm() {
	return RCPM::instance();
}

// Get Recipe Manager Running
rcpm();

/**
 * Plugin Activation hook function to check for Minimum PHP and WordPress versions
 *
 * Cannot use static:: in case php 5.2 is used.
 */
function rcpm_activation_check() {
	global $wp_version;

	if ( version_compare( PHP_VERSION, RCPM::$MIN_PHP_VER, '<' ) ) {
		$flag = 'PHP';
	} elseif ( version_compare( $wp_version, RCPM::$MIN_WP_VER, '<' ) ) {
		$flag = 'WordPress';
	} else {
		return;
	}

	$version = 'PHP' == $flag ? RCPM::$MIN_PHP_VER : RCPM::$MIN_WP_VER;

	// Deactivate automatically due to insufficient PHP or WP Version.
	deactivate_plugins( basename( __FILE__ ) );

	$notice = sprintf( __( 'The %4$s %1$s %5$s plugin requires %2$s version %3$s or greater.', 'recipe-manager' ), RCPM::$NAME, $flag, $version, "<strong>", "</strong>" );

	wp_die( "<p>$notice</p>", __( 'Plugin Activation Error', 'recipe-manager' ), array(
		'response'  => 200,
		'back_link' => true,
	) );
}

// Ensure plugin & environment compatibility.
register_activation_hook( __FILE__, 'rcpm_activation_check' );

// Register activation, deactivation & uninstall hooks.
register_activation_hook( __FILE__, array( '\\RCPM\Activation', 'activate' ) );
register_deactivation_hook( __FILE__, array( '\\RCPM\Activation', 'deactivate' ) );
register_uninstall_hook( __FILE__, array( '\\RCPM\Activation', 'uninstall' ) );
