<?php
/**
 * The Template for displaying recipe archives, including the main recipes page which is a post type archive.
 *
 * Override this template by copying it to yourtheme/rcpm/archive-recipe.php
 *
 * @author 		Daniel Iser
 * @package 	RCPM/Templates
 * @version    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

get_header( 'recipes' ); ?>

<?php
/**
 * rcpm_before_main_content hook
 *
 * @hooked rcpm_output_content_wrapper - 10 (outputs opening divs for the content)
 * @hooked rcpm_breadcrumb - 20
 */
do_action( 'rcpm_before_main_content' );
?>

<?php if ( apply_filters( 'rcpm_show_page_title', true ) ) : ?>

	<h1 class="page-title"><?php rcpm_page_title(); ?></h1>

<?php endif; ?>

<?php
/**
 * rcpm_archive_description hook
 *
 * @hooked rcpm_taxonomy_archive_description - 10
 * @hooked rcpm_recipe_archive_description - 10
 */
do_action( 'rcpm_archive_description' );
?>

<?php if ( have_posts() ) : ?>

	<?php
	/**
	 * rcpm_before_recipes_loop hook
	 *
	 * @hooked rcpm_result_count - 20
	 * @hooked rcpm_catalog_ordering - 30
	 */
	do_action( 'rcpm_before_recipes_loop' );
	?>

	<?php rcpm_recipe_loop_start(); ?>

	<?php //rcpm_recipe_subcategories(); ?>

	<?php while ( have_posts() ) : the_post(); ?>

		<?php rcpm_get_template_part( 'content', 'recipe' ); ?>

	<?php endwhile; // end of the loop. ?>

	<?php rcpm_recipe_loop_end(); ?>

	<?php
	/**
	 * rcpm_after_recipes_loop hook
	 *
	 * @hooked rcpm_pagination - 10
	 */
	do_action( 'rcpm_after_recipes_loop' );
	?>

<?php else : ?>

	<?php rcpm_get_template( 'loop/no-recipes-found.php' ); ?>

<?php endif; ?>

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

<?php get_footer( 'recipes' ); ?>