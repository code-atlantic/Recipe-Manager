<?php
/**
 * Single Recipe Meta
 *
 * @author 		DanielIser
 * @package 	RCPM/Templates
 * @version    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post, $recipe;

?>
<div <?php rcpm_attr( 'recipe-meta' ); ?>>

	<?php do_action( 'rcpm_single_recipe_meta_start' ); ?>

	<?php do_action( 'rcpm_single_recipe_meta' ); ?>

	<?php do_action( 'rcpm_single_recipe_meta_end' ); ?>

</div>