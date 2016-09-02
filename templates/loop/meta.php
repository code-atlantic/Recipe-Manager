<?php
/**
 * Recipe loop meta
 *
 * @author  Daniel Iser
 * @package RCPM/Templates
 * @version    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<div <?php rcpm_attr( 'recipe-meta' ); ?>>

	<?php do_action( 'rcpm_recipes_loop_item_meta' ); ?>

</div>
