<?php
/**
 * Displayed when no recipes are found matching the current query.
 *
 * Override this template by copying it to yourtheme/rcpm/loop/no-recipes-found.php
 *
 * @author 		Daniel Iser
 * @package 	RCPM/Templates
 * @version    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<p class="rcpm-info"><?php _e( 'No recipes were found matching your selection.', 'recipe-manager' ); ?></p>
