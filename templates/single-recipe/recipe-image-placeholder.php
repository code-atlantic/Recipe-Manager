<?php
/**
 * Single Recipe Image Placeholder
 *
 * @author 		DanielIser
 * @package 	RCPM/Templates
 * @version    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post, $recipe;

echo sprintf( '<img ' . rcpm_attr( 'recipe-image', array( 'src' => '%1$s', 'alt' => '%2$s' ), false ) . ' />', rcpm_placeholder_img_src(), __( 'Placeholder', 'recipe-manager' ) );
