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

switch ( $time ) {
	case 'prep':
		$datetime = rcpm_hhmm_to_schema_duration( $recipe->get_prep_time() );
		$label = $recipe->get_prep_time( 'hm' );
		break;
	case 'cook':
		$datetime = rcpm_hhmm_to_schema_duration( $recipe->get_cook_time() );
		$label = $recipe->get_cook_time( 'hm' );
		break;
	case 'total':
		$datetime = rcpm_hhmm_to_schema_duration( $recipe->get_total_time() );
		$label = $recipe->get_total_time( 'hm' );
		break;
	default:
		$datetime = $label = '';
		break;
}

?>

<time <?php rcpm_attr( 'recipe-' . $time . '-time', array( 'datetime' => $datetime ) ); ?>>
	<?php echo $label; ?>
</time>