<?php
/**
 * The template for displaying recipe content in the single-recipe.php template
 *
 * Override this template by copying it to yourtheme/rcpm/content-single-recipe.php
 *
 * @author 		DanielIser
 * @package 	RCPM/Templates
 * @version    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * rcpm_before_single_recipe hook
 *
 * @hooked rcpm_print_notices - 10
 */
do_action( 'rcpm_before_single_recipe' );

if ( post_password_required() ) {
	echo get_the_password_form();
	return;
}
?>

<div <?php rcpm_attr( 'recipe-wrapper', array( 'id' => 'recipe-' . get_the_ID(), 'class' => get_post_class() ) ); ?>>

	<?php
	/**
	 * rcpm_before_single_recipe_summary hook
	 *
	 * @hooked rcpm_show_recipe_sale_flash - 10
	 * @hooked rcpm_show_recipe_images - 20
	 */
	do_action( 'rcpm_before_single_recipe_summary' );
	?>

	<div class="summary entry-summary">

		<?php
		/**
		 * rcpm_single_recipe_summary hook
		 *
		 * @hooked rcpm_template_single_title - 5
		 * @hooked rcpm_template_single_rating - 10
		 * @hooked rcpm_template_single_price - 10
		 * @hooked rcpm_template_single_excerpt - 20
		 * @hooked rcpm_template_single_add_to_cart - 30
		 * @hooked rcpm_template_single_meta - 40
		 * @hooked rcpm_template_single_sharing - 50
		 */
		do_action( 'rcpm_single_recipe_summary' );
		?>

	</div><!-- .summary -->

	<?php
	/**
	 * rcpm_after_single_recipe_summary hook
	 *
	 * @hooked rcpm_output_recipe_data_tabs - 10
	 * @hooked rcpm_upsell_display - 15
	 * @hooked rcpm_output_related_recipes - 20
	 */
	do_action( 'rcpm_after_single_recipe_summary' );
	?>

	<meta itemprop="url" content="<?php the_permalink(); ?>" />
	<meta itemprop="author" content="<?php echo get_the_author(); ?>" />

</div><!-- #recipe-<?php the_ID(); ?> -->

<?php do_action( 'rcpm_after_single_recipe' ); ?>
