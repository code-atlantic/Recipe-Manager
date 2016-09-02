<?php
/**
 * Single Recipe Thumbnail
 *
 * @author 		DanielIser
 * @package 	RCPM/Templates
 * @version    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post, $recipe;

if ( has_post_thumbnail() ) {

	$image_title   = esc_attr( get_the_title( get_post_thumbnail_id() ) );
	$image_caption = get_post( get_post_thumbnail_id() )->post_excerpt;
	$image_link    = wp_get_attachment_url( get_post_thumbnail_id() );
	$image         = get_the_post_thumbnail( $post->ID, apply_filters( 'single_recipe_large_thumbnail_size', rcpm_get_option( 'featured_image_size', 'large' ) ), array(
		'title' => $image_title,
		'alt'   => $image_title
	) );

	$gallery = '';

	$use_links = false;

	if ( $use_links ) {
		echo sprintf( '<a ' . rcpm_attr( 'recipe-image', array( 'href'  => '%1$s', 'title' => '%2$s' ), false ) . '>%3$s</a>', $image_link, $image_caption, $image );
	} else {
		echo sprintf( '<span ' . rcpm_attr( 'recipe-image', null, false ) . '>%s</span>', $image );
	}

}