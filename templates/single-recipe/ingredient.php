<?php
/**
 * Single Recipe Ingredient
 *
 * @author 		DanielIser
 * @package 	RCPM/Templates
 * @version    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed4 directly
}

global $post, $recipe;

?>
<li <?php rcpm_attr( 'recipe-ingredient' ); ?>>

	<?php
		printf(
			'%s %s %s',
			$ingredient['measure'],
			$ingredient['unit'],
			$ingredient['label']
		);
	?>

	<?php if ( $ingredient['note'] != '' ) : ?>

		<span <?php rcpm_attr( 'recipe-ingredient-note' ); ?>><?php esc_html_e( $ingredient['note'] ); ?></span>

	<?php endif; ?>

</li>
