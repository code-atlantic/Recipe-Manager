<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function rcpm_data_attr_filters() {
	add_filter( 'rcpm_attr_recipe-wrapper', 'rcpm_recipe_wrapper_data_attr' );
	/*
	add_filter( 'rcpm_attr_recipe-title', 'rcpm_microdata_recipe_title' );
	add_filter( 'rcpm_attr_recipe-description', 'rcpm_microdata_recipe_description' );
	add_filter( 'rcpm_attr_recipe-prep-time', 'rcpm_microdata_recipe_times', 10, 2 );
	add_filter( 'rcpm_attr_recipe-cook-time', 'rcpm_microdata_recipe_times', 10, 2 );
	add_filter( 'rcpm_attr_recipe-total-time', 'rcpm_microdata_recipe_times', 10, 2 );
	add_filter( 'rcpm_attr_recipe-servings', 'rcpm_microdata_recipe_yield', 10, 2 );
	add_filter( 'rcpm_attr_recipe-ingredient', 'rcpm_microdata_recipe_ingredient', 10, 2 );
	add_filter( 'rcpm_attr_recipe-step', 'rcpm_microdata_recipe_step', 10, 2 );
	add_filter( 'wp_get_attachment_image_attributes', 'rcpm_microdata_post_thumbnail' );
	add_action( 'rcpm_recipe_card_before_content', 'rcpm_microdata_date_published' );
	*/
}
add_action( 'wp_head', 'rcpm_data_attr_filters' );

/**
 * Add Microdata to the recipe description
 *
 * @since 1.0.0
 *
 * @param $attributes
 *
 * @return mixed|void
 */
function rcpm_recipe_wrapper_data_attr( $attributes ) {
	$data_attr = apply_filters( 'recipe_wrapper_data_attr', array(
		'id' => get_the_ID()
	), get_the_ID() );
	foreach ( $data_attr as $name => $value ) {
		$attributes[ 'data-' . $name ] = is_object( $value ) || is_array( $value ) ? json_encode( $value ) : $value;
	}
	$attributes['id'] = 'recipe-' . get_the_ID();
	return $attributes;
}
