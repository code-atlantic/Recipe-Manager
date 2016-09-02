<?php

// Exit if accessed directly
use RCPM\Recipe;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function rcpm_get_the_recipe( $recipe_id = 0 ) {
	if ( empty( $recipe_id ) ) {
		$recipe_id = get_the_ID();
	}

	$recipe = new Recipe( $recipe_id );

	if ( ! $recipe->is_valid() ) {
		return null;
	}

	return $recipe;
}

/**
 * Retrieve a recipe by a given field
 *
 * @since       1.0.0
 *
 * @param       string $field The field to retrieve the recipe with
 * @param       mixed $value The value for field
 *
 * @return      mixed
 */
function rcpm_get_recipe_by( $field = '', $value = '' ) {

	if ( empty( $field ) || empty( $value ) ) {
		return false;
	}

	switch ( strtolower( $field ) ) {

		case 'id':
			$recipe = get_post( $value );

			if ( get_post_type( $recipe ) != 'recipe' ) {
				return false;
			}

			break;

		case 'slug':
		case 'name':
			$recipe = get_posts( array(
				'post_type'      => 'recipe',
				'name'           => $value,
				'posts_per_page' => 1,
				'post_status'    => 'any'
			) );

			if ( $recipe ) {
				$recipe = $recipe[0];
			}

			break;

		default:
			return false;
	}

	if ( $recipe ) {
		return rcpm_get_the_recipe( $recipe->ID );
	}

	return false;
}

/**
 * Retrieves a recipe post object by ID or slug.
 *
 * @since 1.0.0
 *
 * @param int $recipe Recipe ID
 *
 * @return WP_Post $recipe Entire recipe data
 */
function rcpm_get_recipe( $recipe = 0 ) {
	if ( is_numeric( $recipe ) ) {
		$field = 'id';
	} else {
		$field = 'slug';
	}

	return rcpm_get_recipe_by( $field, $recipe );
}


/**
 * @param int $recipe_id
 *
 * @since 1.0.0
 *
 * @return array|mixed
 */
function rcpm_recipe_get_ingredients( $recipe_id = 0 ) {
	if ( ! ( $recipe = rcpm_get_the_recipe( $recipe_id ) ) ) {
		return array();
	}

	return $recipe->get_ingredients();
}

function rcpm_recipe_get_phases( $recipe_id = 0 ) {
	if ( ! ( $recipe = rcpm_get_the_recipe( $recipe_id ) ) ) {
		return array();
	}

	return $recipe->get_phases();
}

function rcpm_recipe_get_all_ingredients( $recipe_id = 0 ) {
	if ( ! ( $recipe = rcpm_get_the_recipe( $recipe_id ) ) ) {
		return array();
	}

	return $recipe->get_all_ingredients();
}

function rcpm_recipe_get_phase_ingredients( $recipe_id = 0, $phase_key = null ) {
	if ( ! ( $recipe = rcpm_get_the_recipe( $recipe_id ) ) ) {
		return array();
	}

	return $recipe->get_phase_ingredients( $phase_key );
}


