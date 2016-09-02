<?php

// Exit if accessed directly
namespace RCPM;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class RCPM_Recipe
 *
 * @since 1.0.0
 */
class Recipe extends Post {

	protected $required_post_type = 'recipe';

	private $_phases = null;
	private $_ingredients = null;
	private $_allergens = null;

	/**
	 * Convert object to array.
	 *
	 * @since 1.0.0
	 *
	 * @return array Object as array.
	 */
	public function to_array() {
		$recipe = parent::to_array();

		foreach ( array( 'ingredients' ) as $key ) {
			if ( $this->__isset( $key ) ) {
				$recipe[ $key ] = $this->__get( $key );
			}
		}

		return $recipe;
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
		if ( 'ingredients' == $key ) {
			return true;
		}

		return false;
	}

	public function get_phase_ingredients( $phase_key ) {

		$phase = $this->get_phase( $phase_key );

		if ( ! $phase || empty( $phase['ingredients'] ) ) {
			return array();
		}

		$ingredients = array();

		foreach ( $phase['ingredients'] as $ingredient ) {
			$ingredients[] = $ingredient;
		}

		if ( count( $ingredients ) ) {
			$this->get_ingredients();
		}

		return apply_filters( 'rcpm_recipe_get_phase_ingredients', $ingredients, $phase, $this->ID );

	}

	public function get_phase( $phase_key ) {

		$phases = $this->get_phases();

		foreach ( $phases as $phase ) {
			if ( $phase_key == $phase['key'] ) {
				return $phase;
			}
		}

		return false;
	}

	/**
	 * Returns array of ingredients as RCPM_Ingredient post objects.
	 *
	 * @since 1.0.0
	 *
	 * @return array|mixed
	 */
	public function get_ingredients() {

		if ( ! $this->_ingredients ) {

			$this->_ingredients = rcpm()->partner_tables->recipe_ingredients->get_ingredients( $this->ID );

			if ( empty( $this->_ingredients ) ) {
				$this->_ingredients = array();
			}

		}

		return apply_filters( 'rcpm_recipe_get_ingredients', $this->_ingredients, $this->ID );
	}

	public function get_phases() {

		if ( ! $this->_phases ) {

			$phases = get_post_meta( $this->ID, 'phases', true );

			if ( empty( $phases ) ) {
				return array();
			} else {
				foreach ( $phases as $index => $phase ) {
					$phases[ $index ] = array_merge( $phase, get_post_meta( $this->ID, 'phase_' . $phase['key'], true ) );
				}
			}

			$this->_phases = $phases;

		}

		return apply_filters( 'rcpm_recipe_get_phases', $this->_phases, $this->ID );

	}

	public function get_all_ingredients() {

		$phases = $this->get_phases();

		$ingredients = array();

		foreach ( $phases as $phase ) {
			foreach ( $phase['ingredients'] as $ingredient ) {
				$ingredients[] = $ingredient;
			}
		}

		if ( count( $ingredients ) ) {
			$this->get_ingredients();
		}

		return apply_filters( 'rcpm_recipe_get_all_ingredients', $ingredients, $this->ID );

	}

	public function get_courses() {
		$courses = get_the_terms( $this->ID, 'recipe_course' );

		return apply_filters( 'rcpm_recipe_get_courses', $courses, $this->ID );
	}

	public function get_course_list( $before = '', $sep = '', $after = '' ) {
		return get_the_term_list( $this->ID, 'recipe_course', $before, $sep, $after );
	}

	public function get_servings() {
		return apply_filters( 'rcpm_recipe_get_servings', get_post_meta( $this->ID, 'servings', true ), $this->ID );
	}

	/**
	 * Get the recipe prep time.
	 *
	 * @since 1.0.0
	 *
	 * @param string $format
	 *
	 * @return string|void
	 */
	public function get_prep_time( $format = 'hhmm' ) {
		$prep_time = $this->convert_time( get_post_meta( $this->ID, 'prep_time', true ), $format );

		return apply_filters( 'rcpm_recipe_get_prep_time', $prep_time, $format, $this->ID );
	}

	public function convert_time( $time, $format = 'hhmm' ) {
		switch ( $format ) {
			case 'seconds':
				$time = rcpm_hhmm_to_seconds( $time );
				break;
			case 'hm':
				$time = rcpm_hhmm_to_hm( $time );
				break;
			case 'hrmin':
				$time = rcpm_hhmm_to_hrmin( $time );
				break;
		}

		return $time;
	}

	/**
	 * Get the recipe cook time.
	 *
	 * @since 1.0.0
	 *
	 * @param string $format
	 *
	 * @return string|void
	 */
	public function get_cook_time( $format = 'hhmm' ) {
		$cook_time = $this->convert_time( get_post_meta( $this->ID, 'cook_time', true ), $format );

		return apply_filters( 'rcpm_recipe_get_cook_time', $cook_time, $format, $this->ID );
	}

	/**
	 * Get the recipe total time.
	 *
	 * @since 1.0.0
	 *
	 * @param string $format
	 *
	 * @return string|void
	 */
	public function get_total_time( $format = 'hhmm' ) {
		$total_time = $this->convert_time( get_post_meta( $this->ID, 'total_time', true ), $format );

		return apply_filters( 'rcpm_recipe_get_total_time', $total_time, $format, $this->ID );
	}

	public function get_additional_notes() {
		return apply_filters( 'rcpm_recipe_get_additional_notes', get_post_meta( $this->ID, 'additional_notes', true ), $this->ID );
	}

	public function is_allergy_free() {
		return ! $this->has_allergens();
	}

	public function has_allergens() {
		return count( $this->get_allergens() ) > 0;
	}

	public function get_allergens() {
		if ( ! $this->_allergens ) {

			$ingredients = $this->get_ingredients();

			$this->_allergens = array();

			foreach ( $ingredients as $ingredient ) {
				if ( $ingredient->is_allergenic() ) {
					$this->_allergens[] = $ingredient;
				}
			}

		}

		return apply_filters( 'rcpm_recipe_get_allergens', $this->_allergens, $this->ID );
	}


	/**
	 * Returns whether or not the recipe is visible in the loop.
	 *
	 * @return bool
	 */
	public function is_visible() {
		if ( ! $this->ID ) {
			$visible = false;
			// Published/private
		} elseif ( $this->post_status !== 'publish' && ! current_user_can( 'edit_post', $this->id ) ) {
			$visible = false;

			// visibility setting
		} elseif ( 'hidden' === $this->visibility ) {
			$visible = false;
		} elseif ( 'visible' === $this->visibility ) {
			$visible = true;

			// Visibility in loop
		} elseif ( is_search() ) {
			$visible = 'search' === $this->visibility;
		} else {
			$visible = true;
		}

		return apply_filters( 'rcpm_recipe_is_visible', $visible, $this->ID );
	}


}
