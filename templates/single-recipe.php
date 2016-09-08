<?php
/**
 * The Template for displaying all single recipes.
 *
 * Override this template by copying it to yourtheme/rcpm/single-recipe.php
 *
 * @author 		DanielIser
 * @package 	RCPM/Templates
 * @version    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

get_header( 'recipe' ); ?>

	<?php
		/**
		 * rcpm_before_main_content hook
		 *
		 * @hooked rcpm_output_content_wrapper - 10 (outputs opening divs for the content)
		 * @hooked rcpm_breadcrumb - 20
		 */
		do_action( 'rcpm_before_main_content' );
	?>

	<?php while ( have_posts() ) : the_post(); ?>

		<?php rcpm_get_template_part( 'content', 'single-recipe' ); ?>

	<?php endwhile; // end of the loop. ?>

	<?php
		/**
		 * rcpm_after_main_content hook
		 *
		 * @hooked rcpm_output_content_wrapper_end - 10 (outputs closing divs for the content)
		 */
		do_action( 'rcpm_after_main_content' );
	?>

	<?php
		/**
		 * rcpm_sidebar hook
		 *
		 * @hooked rcpm_get_sidebar - 10
		 */
		do_action( 'rcpm_sidebar' );
	?>

<?php get_footer( 'recipe' ); ?>
