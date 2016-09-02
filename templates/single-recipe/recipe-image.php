<?php
/**
 * Single Recipe Image
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post, $recipe;

if ( has_post_thumbnail() ) : ?>

	<div class="images">

		<?php rcpm_get_template( 'single-recipe/recipe-thumbnail.php' ); ?>

		<?php do_action( 'rcpm_recipe_thumbnails' ); ?>

	</div>

<?php endif; ?>
