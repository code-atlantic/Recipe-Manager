<?php
/**
 * Shortcode Recipe title
 *
 * @author  DanielIser
 * @package RCPM/Templates
 * @version    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<h1 <?php rcpm_attr( 'recipe-title', array( 'class' => 'entry-title' ) ); ?>><?php the_title(); ?></h1>
