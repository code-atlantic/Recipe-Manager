<?php
/**
 * Content wrappers
 *
 * @author 		DanielIser
 * @package 	RCPM/Templates
 * @version    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$template = get_option( 'template' );

do_action( 'rcpm_before_recipe_wrapper_end' );

switch( $template ) {
	case 'twentyeleven' :
		echo '</div>';
		get_sidebar( 'content' );
		echo '</div>';
		break;
	case 'twentytwelve' :
		echo '</div></div>';
		break;
	case 'twentythirteen' :
		echo '</div></div>';
		break;
	case 'twentyfourteen' :
		echo '</div></div></div>';
		get_sidebar( 'content' );
		break;
	case 'twentyfifteen' :
		echo '</div></div>';
		break;
	case 'twentysixteen' :
		echo '</main></div>';
		break;
	default :
		echo '</div></div>';
		break;
}
