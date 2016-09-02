<?php

// Exit if accessed directly
namespace RCPM;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class RCPM\Ingredient
 *
 * @since 1.0.0
 */
class Ingredient extends Post {

	protected $required_post_type = 'ingredient';

	public function is_allergenic() {
		return get_post_meta( $this->ID, '_is_allergen', true ) == 1;
	}

	public function has_lactose() {
		return get_post_meta( $this->ID, '_has_lactose', true ) == 1;
	}

	public function get_allergy_warning() {
		return apply_filters( 'rcpm_ingredient_get_allergy_warning', get_post_meta( $this->ID, '_allergen_warning', true ), $this->ID );
	}


	/**
	 * Isset-er.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Property to check if set.
	 *
	 * @return bool
	 */
	public function __isset( $key ) {
		if ( 'recipes' == $key ) {
			return true;
		}

		return false;
	}

	/**
	 * Convert object to array.
	 *
	 * @since 1.0.0
	 *
	 * @return array Object as array.
	 */
	public function to_array() {
		$recipe = parent::to_array();

		foreach ( array( 'recipes' ) as $key ) {
			if ( $this->__isset( $key ) ) {
				$recipe[ $key ] = $this->__get( $key );
			}
		}

		return $recipe;
	}


	/**
	 * Returns array of recipes as RCPM_Recipe post objects.
	 *
	 * @since 1.0.0
	 *
	 * @return array|mixed
	 */
	public function get_recipes() {
		if ( ! $this->is_valid() ) {
			return array();
		}

		$recipes = rcpm()->partner_tables->recipe_ingredients->get_recipes( $this->ID );

		if ( empty( $recipes ) ) {
			return array();
		}

		return $recipes;
	}

}
