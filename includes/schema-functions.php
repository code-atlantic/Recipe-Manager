<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Should we add schema.org microdata?
 *
 * @since 1.0.0
 * @return bool
 */
function rcpm_add_schema_microdata() {
	// Don't modify anything until after wp_head() is called
	$ret = (bool) did_action( 'wp_head' );
	return apply_filters( 'rcpm_add_schema_microdata', $ret );
}


function rcpm_microdata_check() {
	if ( rcpm_add_schema_microdata() ) {
		add_filter( 'rcpm_attr_recipe-wrapper', 'rcpm_microdata_recipe_wrapper' );
		add_filter( 'rcpm_attr_recipe-title', 'rcpm_microdata_recipe_title' );
		add_filter( 'rcpm_attr_recipe-image', 'rcpm_microdata_recipe_image' );
		add_filter( 'rcpm_attr_recipe-description', 'rcpm_microdata_recipe_description' );
		add_filter( 'rcpm_attr_recipe-prep-time', 'rcpm_microdata_recipe_times', 10, 2 );
		add_filter( 'rcpm_attr_recipe-cook-time', 'rcpm_microdata_recipe_times', 10, 2 );
		add_filter( 'rcpm_attr_recipe-total-time', 'rcpm_microdata_recipe_times', 10, 2 );
		add_filter( 'rcpm_attr_recipe-ingredient', 'rcpm_microdata_recipe_ingredient', 10, 2 );
		add_filter( 'rcpm_attr_recipe-step', 'rcpm_microdata_recipe_step', 10, 2 );
		add_filter( 'wp_get_attachment_image_attributes', 'rcpm_microdata_post_thumbnail' );
		add_action( 'rcpm_before_recipe_content', 'rcpm_microdata_date_published' );
		add_action( 'rcpm_before_recipe_content', 'rcpm_microdata_nutrition' );
		add_action( 'rcpm_before_recipe_content', 'rcpm_microdata_rating' );
	}
}
add_action( 'wp_head', 'rcpm_microdata_check' );

/**
 * Add schema.org microdata to recipe wrapper.
 *
 * @since 1.0.0
 *
 * @param array $attributes
 *
 * @return array $attributes
 */
function rcpm_microdata_recipe_wrapper( $attributes ) {
	$attributes['itemscope'] = 'itemscope';
	$attributes['itemtype'] = 'http://schema.org/Recipe';
	return $attributes;
}

/**
 * Add schema.org microdata to recipe titles.
 *
 * @since 1.0.0
 *
 * @param array $attributes
 *
 * @return array $attributes
 */
function rcpm_microdata_recipe_title( $attributes ) {
	$attributes['itemprop'] = 'name';
	return $attributes;
}

/**
 * Add Microdata to the recipe description
 *
 * @since 1.0.0
 *
 * @param $attributes
 *
 * @return mixed|void
 */
function rcpm_microdata_recipe_description( $attributes ) {
	$attributes['itemprop'] = 'description';
	return $attributes;
}

/**
 * Add Microdata to the recipe times
 *
 * @since 1.0.0
 *
 * @param $attributes
 * @param $context
 *
 * @return mixed|void
 */
function rcpm_microdata_recipe_times( $attributes, $context ) {
	switch ( $context ) {
		case 'recipe-prep-time':
			$attributes['itemprop'] = 'prepTime';
			break;
		case 'recipe-cook-time':
			$attributes['itemprop'] = 'cookTime';
			break;
		case 'recipe-total-time':
			$attributes['itemprop'] = 'totalTime';
			break;
	}
	return $attributes;
}


/**
 * Add Microdata to the recipe ingredient
 *
 * @since 1.0.0
 *
 * @param $attributes
 *
 * @return mixed|void
 */
function rcpm_microdata_recipe_ingredient( $attributes ) {
	$attributes['itemprop'] = 'ingredients';
	return $attributes;
}

/**
 * Add Microdata to the recipe step
 *
 * @since 1.0.0
 *
 * @param $attributes
 *
 * @return mixed|void
 */
function rcpm_microdata_recipe_step( $attributes ) {
	$attributes['itemprop'] = 'recipeInstructions';
	return $attributes;
}

/**
 * Add Microdata to the recipe images
 *
 * @since 1.0.0
 *
 * @param $attributes
 *
 * @return mixed|void
 */
function rcpm_microdata_recipe_image( $attributes ) {
	$attributes['itemprop'] = 'image';
	return $attributes;
}


/**
 * Add schema.org microdata to recipe post date.
 *
 * @since 1.0.0
 *
 * @param null $post_id
 */
function rcpm_microdata_date_published( $post_id = null ) {
	if ( ! rcpm_add_schema_microdata() ) {
		return;
	}

	printf( '<meta itemprop="datePublished" content="%s" />', get_the_date( 'Y-m-d', $post_id ) );
	printf( '<meta itemprop="dateModified" content="%s" />', get_the_modified_date( 'Y-m-d', $post_id ) );
}

/**
 * Add schema.org microdata to recipe images.
 *
 * @since 1.0.0
 *
 * @param $attr
 *
 * @return mixed
 */
function rcpm_microdata_post_thumbnail( $attributes ) {
	global $post;

	if( ! rcpm_add_schema_microdata() || ! is_object( $post ) ) {
		return $attributes;
	}

	return is_singular( 'recipe' ) ? rcpm_microdata_recipe_image( $attributes ) : $attributes;
}

function rcpm_microdata_nutrition( $post_id ) {
	if ( ! rcpm_add_schema_microdata() ) {
		return;
	}

	printf( '<meta itemprop="nutrition" content="" />' );
}

function rcpm_microdata_rating( $post_id ) {
	if ( ! rcpm_add_schema_microdata() ) {
		return;
	}

	?>

	<span itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating" class="aggregate-rating">
        <meta itemprop="ratingValue" content="0" />
        <meta itemprop="reviewCount" content="0" />
    </span>
	<?php
}
