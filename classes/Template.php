<?php

namespace RCPM;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Template {

	/**
	 * Load a template.
	 *
	 * Handles template usage so that we can use our own templates instead of the themes.
	 *
	 * Templates are in the 'templates' folder. Recipe Manager looks for theme
	 * overrides in /theme/rcpm/ by default
	 *
	 * For beginners, it also looks for a rcpm.php template first. If the user adds
	 * this to the theme (containing a rcpm_content() inside) this will be used for all
	 * rcpm templates.
	 *
	 * @param mixed $template
	 *
	 * @return string
	 */
	public static function loader( $template ) {
		$find = array( 'rcpm.php' );
		$file = '';

		$template_overload = apply_filters( 'rcpm_template_overload', array() );

		if ( ! empty ( $template_overload ) ) {

			$file = $template_overload[0];
			$find = $template_overload;

		} elseif ( is_single() && get_post_type() == 'recipe' ) {

			$file   = 'single-recipe.php';
			$find[] = $file;
			$find[] = rcpm()->template_path() . $file;

		} elseif ( is_single() && get_post_type() == 'ingredient' ) {

			$file   = 'single-ingredient.php';
			$find[] = $file;
			$find[] = rcpm()->template_path() . $file;

		} elseif ( is_tax( 'recipe_course' ) ) {

			$term = get_queried_object();

			$file = 'archive-recipe.php';

			$find[] = 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
			$find[] = rcpm()->template_path() . 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
			$find[] = 'taxonomy-' . $term->taxonomy . '.php';
			$find[] = rcpm()->template_path() . 'taxonomy-' . $term->taxonomy . '.php';
			$find[] = $file;
			$find[] = rcpm()->template_path() . $file;

		} elseif ( is_post_type_archive( 'recipe' ) ) {

			$file   = 'archive-recipe.php';
			$find[] = $file;
			$find[] = rcpm()->template_path() . $file;

		}

		if ( $file ) {
			$template = locate_template( array_unique( $find ) );
			if ( ! $template || RCPM_TEMPLATE_DEBUG_MODE ) {
				$template = rcpm()->plugin_path() . 'templates/' . $file;
			}
		}

		return $template;
	}

}
