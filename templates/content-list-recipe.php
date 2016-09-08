<?php
/**
 * The template for displaying recipe content within loops.
 *
 * Override this template by copying it to yourtheme/rcpm/content-recipe.php
 *
 * @author  Daniel Iser
 * @package RCPM/Templates
 * @version    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $recipe, $rcpm_loop;

// Store loop count we're currently on
if ( empty( $rcpm_loop['loop'] ) ) {
	$rcpm_loop['loop'] = 0;
}

// Store column count for displaying the grid
if ( empty( $rcpm_loop['columns'] ) ) {
	$rcpm_loop['columns'] = apply_filters( 'loop_recipes_columns', 4 );
}

// Ensure visibility
if ( ! $recipe || ! $recipe->is_visible() ) {
	return;
}

// Increase loop count
$rcpm_loop['loop']++;

// Extra post classes
$classes = array();
if ( 0 == ( $rcpm_loop['loop'] - 1 ) % $rcpm_loop['columns'] || 1 == $rcpm_loop['columns'] ) {
	$classes[] = 'first';
}
if ( 0 == $rcpm_loop['loop'] % $rcpm_loop['columns'] ) {
	$classes[] = 'last';
}

?>
<li <?php rcpm_attr( 'recipe-wrapper', array( 'class' => get_post_class( $classes ) ) ); ?>>

	<?php
	do_action( 'rcpm_before_recipes_list_item' );

	/**
	 * rcpm_before_recipes_loop_item_title hook
	 *
	 * @hooked rcpm_template_loop_recipe_thumbnail - 10
	 */
	do_action( 'rcpm_before_recipes_list_item_title' );


	/**
	 * rcpm_recipes_loop_item_title hook
	 *
	 * @hooked rcpm_template_loop_recipe_title - 10
	 */
	do_action( 'rcpm_recipes_list_item_title' );


	/**
	 * rcpm_after_recipes_loop_item_title hook
	 *
	 */
	do_action( 'rcpm_after_recipes_list_item_title' );


	/**
	 * rcpm_after_recipes_loop_item hook
	 *
	 */
	do_action( 'rcpm_after_recipes_list_item' );

	?>

	<meta itemprop="url" content="<?php the_permalink(); ?>" />
	<meta itemprop="author" content="<?php echo get_the_author(); ?>" />

</li>