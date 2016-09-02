<?php

// Exit if accessed directly
use RCPM\Ingredient;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Retrieve a ingredient by a given field
 *
 * @since       1.0.0
 *
 * @param       string $field The field to retrieve the ingredient with
 * @param       mixed $value The value for field
 *
 * @return      mixed
 */
function rcpm_get_ingredient_by( $field = '', $value = '' ) {

	if ( empty( $field ) || empty( $value ) ) {
		return false;
	}

	switch ( strtolower( $field ) ) {

		case 'id':
			$ingredient = get_post( $value );

			if ( get_post_type( $ingredient ) != 'ingredient' ) {
				return false;
			}

			break;

		case 'slug':
		case 'name':
			$ingredient = get_posts( array(
				'post_type'      => 'ingredient',
				'name'           => $value,
				'posts_per_page' => 1,
				'post_status'    => 'any'
			) );

			if ( $ingredient ) {
				$ingredient = $ingredient[0];
			}

			break;

		default:
			return false;
	}

	if ( $ingredient ) {
		return $ingredient;
	}

	return false;
}

/**
 * Retrieves a ingredient post object by ID or slug.
 *
 * @since 1.0.0
 *
 * @param int $ingredient Ingredient ID
 *
 * @return WP_Post $ingredient Entire ingredient data
 */
function rcpm_get_ingredient( $ingredient = 0 ) {
	if ( is_numeric( $ingredient ) ) {
		$field = 'id';
	} else {
		$field = 'slug';
	}

	return rcpm_get_ingredient_by( $field, $ingredient );
}

/**
 * @param int $ingredient_id
 *
 * @since 1.0.0
 *
 * @return array|mixed
 */
function rcpm_get_ingredient_recipes( $ingredient_id = 0 ) {
	if ( empty( $ingredient_id ) ) {
		$ingredient_id = get_the_ID();
	}

	$ingredient = new Ingredient( $ingredient_id );

	if ( ! $ingredient->is_valid() ) {
		return array();
	}

	return $ingredient->get_recipes();
}


function rcpm_get_all_ingredients( $fields = array( 'ID' ) ) {
	global $wpdb;
	natcasesort( $fields );
	$select      = implode( ', ', $fields );
	$ingredients = wp_cache_get( $select, 'get_all_ingredients' );
	if ( ! $ingredients ) {
		$ingredients = $wpdb->get_results( "SELECT $select FROM $wpdb->posts WHERE post_type = 'ingredient' AND post_status = 'publish';" );
		wp_cache_add( $select, $ingredients, 'get_all_ingredients' );
	}

	return $ingredients;
}