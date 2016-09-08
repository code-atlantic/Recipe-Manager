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
	<?php if ( rcpm_get_option( 'disable_public_recipes', false ) ) : ?>
		<?php the_title(); ?>
	<?php else : ?>
		<a href="<?php the_permalink(); ?>">
			<?php the_title(); ?>
		</a>
	<?php endif; ?>
</h3><?php
