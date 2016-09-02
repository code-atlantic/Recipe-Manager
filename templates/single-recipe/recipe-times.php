<?php
/**
 * Single Recipe Times
 *
 * @author 		DanielIser
 * @package 	RCPM/Templates
 * @version    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post, $recipe;



/**
 * `rcpm_before_recipe_times` Fires before the recipe times.
 */
do_action( 'rcpm_before_recipe_times', $recipe->ID ); ?>

<ul <?php rcpm_attr( 'recipe-times' ); ?>>

	<?php
	/**
	 * `rcpm_before_recipe_time_list` Fires before the list of prep times.
	 */
	do_action( 'rcpm_before_recipe_time_list', $recipe->ID ); ?>


	<li <?php rcpm_attr( 'recipe-times-prep' ); ?>>

		<?php
		/**
		 * Recipe Prep Time
		 */
		printf( '<strong>%s</strong> ', rcpm_recipe_labels( 'prep' ) );
		rcpm_get_template( 'single-recipe/time.php', array( 'time' => 'prep' ) ); ?>

	</li>

	<li <?php rcpm_attr( 'recipe-times-cook' ); ?>>

		<?php
		/**
		 * Recipe Cook Time
		 */
		printf( '<strong>%s</strong> ', rcpm_recipe_labels( 'cook' ) );
		rcpm_get_template( 'single-recipe/time.php', array( 'time' => 'cook' ) ); ?>

	</li>

	<li <?php rcpm_attr( 'recipe-times-total' ); ?>>

		<?php
		/**
		 * Recipe Total Time
		 */
		printf( '<strong>%s</strong> ', rcpm_recipe_labels( 'total' ) );
		rcpm_get_template( 'single-recipe/time.php', array( 'time' => 'total' ) ); ?>

	</li>

	<?php
	/**
	 * `rcpm_after_recipe_time_list` Fires after the list of prep times.
	 */
	do_action( 'rcpm_after_recipe_time_list', $recipe->ID ); ?>

</ul>

<?php
/**
 * `rcpm_after_recipe_times` Fires after the recipe times.
 */
do_action( 'rcpm_after_recipe_times', $recipe->ID );
