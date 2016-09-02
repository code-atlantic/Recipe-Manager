<?php
/**
 * Single Recipe Meta: Courses
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post, $recipe;

$label = rcpm_recipe_labels( 'courses' );

$args = array(
	'before' => $label != '' ? "<strong>$label: </strong>" : '',
	'sep'    => ', ',
	'after' => '',
);

$course_list = $recipe->get_course_list( $args['before'], $args['sep'], $args['after'] );

if ( is_wp_error( $course_list ) ) {
	return;
}

?>

<div <?php rcpm_attr( 'recipe-courses' ); ?>>
	<?php echo $course_list; ?>
</div>