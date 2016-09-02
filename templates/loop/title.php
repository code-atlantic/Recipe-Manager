<?php
/**
 * Recipe loop title
 *
 * @author  Daniel Iser
 * @package RCPM/Templates
 * @version    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<h3 <?php rcpm_attr( 'recipe-title' ); ?>>
	<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
</h3><?php
