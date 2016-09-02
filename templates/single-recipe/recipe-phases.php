<?php
/**
 * Single Recipe Phases
 *
 * @author 		DanielIser
 * @package 	RCPM/Templates
 * @version    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post, $recipe, $phase, $ingredient, $step;

?>
<div <?php rcpm_attr( 'recipe-phases' ); ?>>

	<?php do_action( 'rcpm_before_recipe_phases', $recipe->ID ); ?>

	<?php foreach ( $recipe->get_phases() as $index => $phase ) : ?>

	<div <?php rcpm_attr( 'recipe-phase' ); ?>>

		<?php if ( $phase['name'] != '' ) : ?>

		<h3 <?php rcpm_attr( 'recipe-phase-name' ); ?>>
			<?php echo apply_filters( 'recipe_phase_name', $phase['name'] ); ?>
		</h3>

		<?php endif; ?>

		<?php if ( $phase['description'] != '' ) : ?>

		<p <?php rcpm_attr( 'recipe-phase-description' ); ?>>
			<?php esc_html_e( apply_filters( 'recipe_phase_description', $phase['description'] ) ); ?>
		</p>

		<?php endif; ?>

		<?php if ( rcpm_get_option( 'display_ingredient_list', 'per_phase' ) == 'per_phase' ) : ?>

			<?php rcpm_get_template( 'single-recipe/ingredients.php', array( 'ingredients' => $phase['ingredients'] ) ); ?>

		<?php endif; ?>

		<?php if ( ! empty( $phase['steps'] ) ) : ?>

			<h5 <?php rcpm_attr( 'recipe-step-heading' ); ?>>
				<?php echo rcpm_recipe_labels( 'steps' ); ?>
			</h5>

			<ol <?php rcpm_attr( 'recipe-steps' ); ?>>

				<?php foreach ( $phase['steps'] as $step ) : ?>

				<li <?php rcpm_attr( 'recipe-step' ); ?>>

					<?php esc_html_e( $step['description'] ); ?>

					<?php if ( $step['note'] != '' ) : ?>
					<span <?php rcpm_attr( 'recipe-step-note' ); ?>>

						<?php esc_html_e( $step['note'] ); ?>

					</span>
					<?php endif; ?>

				</li>

				<?php endforeach; ?>

			</ol>

		<?php endif; ?>

	</div>

	<?php endforeach; ?>

	<?php do_action( 'rcpm_after_recipe_phases', $recipe->ID ); ?>

</div>
