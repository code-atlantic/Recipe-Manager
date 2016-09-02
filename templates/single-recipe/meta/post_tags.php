<?php
/**
 * Single Recipe Meta: Post Tags
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post, $recipe;

$label = rcpm_recipe_labels( 'post_tags' );

$args = array(
	'before' => $label != '' ? "<strong>$label: </strong>" : '',
	'sep'    => ', ',
	'after' => '',
);

$tag_list = get_the_tag_list( $args['before'], $args['sep'], $args['after'], $recipe->ID );

if ( is_wp_error( $tag_list ) ) {
	return;
}

?>

<div <?php rcpm_attr( 'recipe-tags' ); ?>>
	<?php echo $tag_list; ?>
</div>