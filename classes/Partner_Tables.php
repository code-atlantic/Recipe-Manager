<?php

// Exit if accessed directly
namespace RCPM;

use RCPM\Partner_Tables\Recipe_Ingredients;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class RCPM\Partner_Tables
 */
class Partner_Tables {


	/**
	 * Recipe Ingredient relationship table.
	 *
	 * @since 1.0.0
	 */
	public $recipe_ingredients;

	/**
	 * Hook the initialize method to the WP init action.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->recipe_ingredients = new Recipe_Ingredients;

	}


}
