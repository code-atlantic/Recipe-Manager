<?php
/**
 * Single Recipe Action Bar
 *
 * @author 		DanielIser
 * @package 	RCPM/Templates
 * @version    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post, $recipe;

if ( ! has_action( 'rcpm_recipe_action_bar' ) ) {
	return;
}

?>
<div <?php rcpm_attr( 'recipe-actions', array( 'class' => 'recipe-action-bar' ) ); ?>>

	<?php do_action( 'rcpm_recipe_action_bar_start' ); ?>

	<?php do_action( 'rcpm_recipe_action_bar' ); ?>

	<?php do_action( 'rcpm_recipe_action_bar_end' ); ?>

</div>