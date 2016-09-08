<?php

// Exit if accessed directly
namespace RCPM;

use WP_Query;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Shortcodes {

	public static function init() {
		add_shortcode( 'recipe', array( __CLASS__, 'recipe' ) );
	}

	public static function recipe( $atts = array() ) {
		global $post;

		if ( empty( $atts ) ) {
			return '';
		}

		$atts = shortcode_atts( array(
			'id'    => null,
			'slug'  => null,
			'full'  => null,
			'class' => '',
		), $atts, 'recipe' );

		$args = array(
			'post_type'      => 'recipe',
			'posts_per_page' => 1,
			'no_found_rows'  => 1,
			'post_status'    => 'publish',
		);

		if ( isset( $atts['id'] ) ) {
			if ( is_numeric( $atts['id'] ) ) {
				$args['p'] = $atts['id'];
			} else {
				$args['name'] = $atts['id'];
			}
		} elseif ( isset( $atts['slug'] ) ) {
			$args['name'] = $atts['id'];
		}

		ob_start();

		$recipes = new WP_Query( apply_filters( 'rcpm_shortcode_recipe_query', $args, $atts ) );

		if ( $recipes->have_posts() ) : ?>

			<?php if ( $atts['full'] === null ) { ?>

				<?php rcpm_recipe_loop_start(); ?>

				<?php while ( $recipes->have_posts() ) : $recipes->the_post(); ?>

					<?php rcpm_get_template_part( 'content', 'recipe' ); ?>

				<?php endwhile; // end of the loop. ?>

				<?php rcpm_recipe_loop_end(); ?>

			<?php } else { ?>

				<?php while ( $recipes->have_posts() ) : $recipes->the_post(); ?>

					<?php rcpm_get_template_part( 'content', 'shortcode-recipe' ); ?>

				<?php endwhile; // end of the loop. ?>

			<?php } ?>

		<?php endif;

		wp_reset_postdata();

		$css_class = 'recipe-shortcode';

		if ( $atts['full'] !== null ) {
			$css_class .= ' full-recipe';
		}

		if ( isset( $atts['class'] ) ) {
			$css_class .= ' ' . $atts['class'];
		}

		return '<div class="' . esc_attr( $css_class ) . '">' . ob_get_clean() . '</div>';

	}

}
