<?php
/**
 * Single Recipe Description
 *
 * @author 		DanielIser
 * @package 	RCPM/Templates
 * @version    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post, $recipe;

do_action( 'rcpm_before_recipe_content', $recipe->ID ); ?>

<div <?php rcpm_attr( 'recipe-description' ); ?>>
	<?php the_content(); ?>
</div>

<?php do_action( 'rcpm_after_recipe_content', $recipe->ID ); ?>
