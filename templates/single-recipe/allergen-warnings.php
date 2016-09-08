<?php
/**
 * Single Recipe: Allergen Warnings
 *
 * @author 		DanielIser
 * @package 	RCPM/Templates
 * @version    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post, $recipe;

if ( $recipe->is_allergy_free() ) {
	return;
}

$allergens = $recipe->get_allergens();

foreach ( $allergens as $ingredient ) : ?>

	<div <?php rcpm_attr( 'recipe-allergy-warning' ); ?>>

		<?php
			$message = $ingredient->get_allergy_warning() != '' ? $ingredient->get_allergy_warning() : sprintf( __( 'Contains %1s which may cause allergic reactions.', 'recipe-manager' ), $ingredient->post_title );
			printf(
				'<strong>%1$s: </strong> %2$s',
				__( 'Warning', 'recipe-manager' ),
				$message
			);
		?>

	</div>

<?php endforeach; ?>
