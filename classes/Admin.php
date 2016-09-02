<?php

// Exit if accessed directly
namespace RCPM;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Admin {

	public static function init() {
		// General
		\RCPM\Admin\Pages::init();
		\RCPM\Admin\Assets::init();

		// Recipes
		\RCPM\Admin\Recipes\Metaboxes::init();
		\RCPM\Admin\Recipes\Help::init();

		// Ingredients
		\RCPM\Admin\Ingredients\Metaboxes::init();
	}

}
