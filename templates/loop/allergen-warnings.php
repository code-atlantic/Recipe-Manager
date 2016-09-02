<?php
/**
 * Recipe Loop Allergens
 *
 * @author 		DanielIser
 * @package 	RCPM/Templates
 * @version    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post, $recipe;

if ( $recipe->is_allergy_free() ) {
	return;
}

?>

<span <?php rcpm_attr( 'allergen-warning' ); ?>><?php
	printf( '<i class="dashicons dashicons-warning" title="%1$s"></i> %1$s ', __( 'Allergen Warning: ', 'recipe-manager' ) );

	$allergens = array();

	foreach ( $recipe->get_allergens() as $ingredient ) {
		$allergens[ $ingredient->post_title ] = $ingredient->post_title;
	}

	echo implode( ',', $allergens ); ?>

</span>

