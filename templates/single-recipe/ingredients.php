<?php
/**
 * Single Recipe Phase Ingredients Loop
 *
 * @author 		DanielIser
 * @package 	RCPM/Templates
 * @version    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post, $recipe;

if ( empty( $ingredients ) ) {
	return;
}

?>

<h5 <?php rcpm_attr( 'recipe-ingredient-heading' ); ?>>
	<?php echo rcpm_recipe_labels( 'ingredients' ); ?>
</h5>

<ul <?php rcpm_attr( 'recipe-ingredients' ); ?>>

	<?php foreach ( $ingredients as $ingredient ) : ?>
		<?php rcpm_get_template( 'single-recipe/ingredient.php', array( 'ingredient' => $ingredient ) ); ?>
	<?php endforeach; ?>

</ul>
