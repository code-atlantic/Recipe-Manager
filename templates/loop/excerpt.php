<?php
/**
 * Recipe loop tme
 *
 * @author  Daniel Iser
 * @package RCPM/Templates
 * @version    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<p <?php rcpm_attr( 'recipe-description' ); ?>>

	<?php echo get_the_excerpt(); ?>

</p>
