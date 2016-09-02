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
<span <?php rcpm_attr( 'recipe-total-time' ); ?>>

	<?php
	/**
	 * Recipe Total Time
	 */

	printf( '<i class="dashicons dashicons-clock" title="%1$s"><span>%1$s</span></i> ', rcpm_recipe_labels( 'total' ) );
	rcpm_get_template( 'single-recipe/time.php', array( 'time' => 'total' ) ); ?>

</span>
