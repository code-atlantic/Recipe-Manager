<?php
/**
 * Single Recipe Servings
 *
 * @author 		DanielIser
 * @package 	RCPM/Templates
 * @version    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post, $recipe;

$servings = $recipe->get_servings();

?>
<span <?php rcpm_attr( 'recipe-servings' ); ?>>

	<meta itemprop="recipeYield" content="<?php echo $servings; ?>" />

	<?php
		printf(
			apply_filters( 'rcpm_recipe_servings_format', '<strong>%1$s: </strong> %2$s' ),
			rcpm_recipe_labels( 'servings' ),
			$servings
		);
	?>

</span>