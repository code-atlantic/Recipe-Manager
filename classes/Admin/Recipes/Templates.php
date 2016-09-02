<?php


namespace RCPM\Admin\Recipes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class Templates {

	public static function admin_footer() {
		global $pagenow, $typenow; ?>
		<script type="text/template" id="recipe-card-ingredient-templ">
			<?php static::ingredient(); ?>
		</script>

		<script type="text/template" id="recipe-card-step-templ">
			<?php static::step(); ?>
		</script>

		<script type="text/template" id="recipe-card-phase-templ"><?php
			$phase = array(
				'index' => '<%= phaseIndex %>',
				'number' => '<%= phaseNumber %>',
			);
			static::phase( $phase ); ?>
		</script><?php
	}

	public static function phase( $phase = array() ) {
		$phase = wp_parse_args( $phase, array(
			'index'       => 0,
			'number'      => 1,
			'name'        => '',
			'description' => '',
			'key'         => '',
			'ingredients' => array(
				array(
					'measure' => '',
					'unit'    => '',
					'ID'      => 0,
					'label'   => '',
					'note'    => '',
				),
			),
			'steps'       => array(
				array(
					'description' => '',
					'note'        => '',
				),
			),
		) );
		?>
		<div class="phase" data-phaseindex="<?php echo $phase['index']; ?>">
			<div class="phase-title" data-target="#phase-<?php echo $phase['number']; ?>">
				<span class="dashicons dashicons-menu"></span>
				<span class="title"><?php _e( 'Phase', 'recipe-manager' ); ?><?php echo $phase['number']; ?></span>
				<span class="remove-phase dashicons dashicons-dismiss" title="<?php _e( 'Remove Phase', 'recipe-manager' ); ?>"></span>
			</div>

			<div id="phase-<?php echo $phase['number']; ?>" class="phase-content">
				<input class="phase-name" type="text" name="recipe_card[<?php echo $phase['index']; ?>][name]" placeholder="<?php _e( 'Phase Name: ex. Batter, Frosting etc', 'recipe-manager' ); ?>" value="<?php esc_attr_e( $phase['name'] ); ?>" />
				<textarea class="phase-description" name="recipe_card[<?php echo $phase['index']; ?>][description]" rows="1" placeholder="<?php _e( 'Phase Description', 'recipe-manager' ); ?>"><?php esc_html_e( $phase['description'] ); ?></textarea>
				<input type="hidden" name="recipe_card[<?php echo $phase['index']; ?>][key]" value="<?php esc_attr_e( $phase['key'] ); ?>" />
				<h2 class="dashicons-before dashicons-carrot">
					<?php _e( 'Ingredients', 'recipe-manager' ); ?>
					<?php do_action( 'rcpm_after_recipe_editor_ingredients_heading', $phase ); ?>
				</h2>
				<table class="recipe-ingredients sortable">
					<thead>
					<tr>
						<th width="28"><?php _e( 'Sort', 'recipe-manager' ); ?></th>
						<th width="65"><?php _e( 'Measure', 'recipe-manager' ); ?></th>
						<th width="70"><?php _e( 'Unit', 'recipe-manager' ); ?></th>
						<th><?php _e( 'Ingredient', 'recipe-manager' ); ?></th>
						<th><?php _e( 'Note', 'recipe-manager' ); ?></th>
						<th width="45"></th>
					</tr>
					</thead>
					<tbody><?php
					if ( empty( $phase['ingredients'] ) ) {
						$phase['ingredients'] = array( array() );
					}

					foreach ( $phase['ingredients'] as $ingredient_index => $ingredient ) :
						$ingredient['phase'] = $phase['index'];
						$inrgedient['index'] = $ingredient_index;
						Templates::ingredient( $ingredient );
					endforeach; ?>
					</tbody>
				</table>
				<h2 class="dashicons-before dashicons-editor-ol">
					<?php _e( 'Steps', 'recipe-manager' ); ?>
					<?php do_action( 'rcpm_after_recipe_editor_ingredients_heading', $phase ); ?>
				</h2>
				<table class="recipe-steps sortable">
					<thead>
					<tr>
						<th width="28"><?php _e( 'Sort', 'recipe-manager' ); ?></th>
						<th><?php _e( 'Description', 'recipe-manager' ); ?></th>
						<th><?php _e( 'Note', 'recipe-manager' ); ?></th>
						<th width="45"></th>
					</tr>
					</thead>
					<tbody><?php
					if ( empty( $phase['steps'] ) ) {
						$phase['steps'] = array( array() );
					}
					foreach ( $phase['steps'] as $step_index => $step ) :
						$step['phase'] = $phase['index'];
						$step['index'] = $step_index;
						Templates::step( $step );
					endforeach; ?>
					</tbody>
				</table>
			</div>
			<!--end .phase-content-->
		</div>        <!--end .phase-->
		<?php
	}

	public static function ingredient( $ingredient = array() ) {
		$ingredient = wp_parse_args( $ingredient, array(
			'phase'   => 0,
			'index'   => 0,
			'measure' => '',
			'unit'    => '',
			'ID'      => 0,
			'label'   => '',
			'note'    => '',
		) );
		?>
		<tr data-ingredientindex="<?php echo $ingredient['index']; ?>">
			<td class="sort-handle">
				<span class="dashicons dashicons-menu"></span>
			</td>
			<td>
				<input class="ingredient-measure" type="text" name="recipe_card[<?php echo $ingredient['phase']; ?>][ingredients][<?php echo $ingredient['index']; ?>][measure]" value="<?php esc_attr_e( $ingredient['measure'] ); ?>" />
			</td>
			<td>
				<input class="ingredient-unit" type="hidden" name="recipe_card[<?php echo $ingredient['phase']; ?>][ingredients][<?php echo $ingredient['index']; ?>][unit]" value="<?php esc_attr_e( $ingredient['unit'] ); ?>" />
				<input class="ingredient-unit-autocomplete" type="text" value="<?php echo _n( rcpm_get_measurement_unit( $ingredient['unit'], 'singular' ), rcpm_get_measurement_unit( $ingredient['unit'], 'plural' ), $ingredient['measure'] ); ?>" />
			</td>
			<td>
				<input class="ingredient-id" type="hidden" name="recipe_card[<?php echo $ingredient['phase']; ?>][ingredients][<?php echo $ingredient['index']; ?>][ID]" value="<?php esc_attr_e( $ingredient['ID'] ); ?>" />
				<input class="ingredient-label" type="text" name="recipe_card[<?php echo $ingredient['phase']; ?>][ingredients][<?php echo $ingredient['index']; ?>][label]" value="<?php esc_attr_e( $ingredient['label'] ); ?>" />
			</td>
			<td>
				<input class="ingredient-note" type="text" name="recipe_card[<?php echo $ingredient['phase']; ?>][ingredients][<?php echo $ingredient['index']; ?>][note]" value="<?php esc_attr_e( $ingredient['note'] ); ?>" />
			</td>
			<td class="recipe-actions">
				<button class="add-ingredient dashicons dashicons-plus-alt" title="<?php _e( 'Add Ingredient', 'recipe-manager' ); ?>"></button>
				<span class="remove-ingredient dashicons dashicons-dismiss" title="<?php _e( 'Remove Ingredient', 'recipe-manager' ); ?>"></span>
			</td>
		</tr>
		<?php
	}

	public static function step( $step = array() ) {
		$step = wp_parse_args( $step, array(
			'phase'       => 0,
			'index'       => 0,
			'description' => '',
			'note'        => '',
		) );
		?>
		<tr data-stepindex="<?php echo $step['index']; ?>">
			<td class="sort-handle">
				<span class="dashicons dashicons-menu"></span>
			</td>
			<td>
				<textarea class="step-description" rows="1" name="recipe_card[<?php echo $step['phase']; ?>][steps][<?php echo $step['index']; ?>][description]"><?php esc_html_e( $step['description'] ); ?></textarea>
			</td>
			<td>
				<input class="step-note" type="text" name="recipe_card[<?php echo $step['phase']; ?>][steps][<?php echo $step['index']; ?>][note]" value="<?php esc_attr_e( $step['note'] ); ?>" />
			</td>
			<td class="recipe-actions">
				<button class="add-step dashicons dashicons-plus-alt" title="<?php _e( 'Add Step', 'recipe-manager' ); ?>"></button>
				<span class="remove-step dashicons dashicons-dismiss" title="<?php _e( 'Remove Step', 'recipe-manager' ); ?>"></span>
			</td>
		</tr>
		<?php
	}

}
