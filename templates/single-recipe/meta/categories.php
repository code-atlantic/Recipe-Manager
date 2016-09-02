<?php
/**
 * Single Recipe Meta: Categories
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post, $recipe;

$label = rcpm_recipe_labels( 'categories' );

$args = array(
	'before' => $label != '' ? "<strong>$label: </strong>" : '',
	'sep'    => ', ',
	'after' => '',
);

$category_list = get_the_term_list( $recipe->ID, 'category', $args['before'], $args['sep'], $args['after'] );

if ( is_wp_error( $category_list ) ) {
	return;
}

?>

<div <?php rcpm_attr( 'recipe-categories' ); ?>>
	<?php echo $category_list; ?>
</div>