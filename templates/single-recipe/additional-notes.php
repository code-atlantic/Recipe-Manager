<?php
/**
 * Single recipe additional notes
 *
 * @author 		DanielIser
 * @package 	RCPM/Templates
 * @version    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post, $recipe;

if ( $recipe->get_additional_notes() == '' ) {
	return;
}

?>

<h4 <?php rcpm_attr( 'recipe-note-heading' ); ?>>

	<?php echo rcpm_recipe_labels( 'additional_notes' ); ?>

</h4>

<p <?php rcpm_attr( 'recipe-notes' ); ?>>

	<?php echo apply_filters( 'rcpm_recipe_additional_notes', $recipe->get_additional_notes(), $recipe->ID ); ?>

</p>
