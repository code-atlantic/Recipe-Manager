<?php
/**
 * Single recipe short description
 *
 * @author 		DanielIser
 * @package 	RCPM/Templates
 * @version    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post;

if ( ! $post->post_excerpt ) {
	return;
}

?>
<div <?php rcpm_attr( 'recipe-description' ); ?>>
	<?php echo apply_filters( 'rcpm_short_description', $post->post_excerpt ) ?>
</div>
