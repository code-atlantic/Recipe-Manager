<?php
/**
 * Recipe Manager Template
 *
 * Functions for the templating system.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use RCPM\Breadcrumb;


/**
 * When the_post is called, put recipe data into a global.
 *
 * @param mixed $post
 */
function rcpm_setup_recipe_data( $post ) {
	unset( $GLOBALS['recipe'] );

	if ( is_int( $post ) ) {
		$post = get_post( $post );
	}

	if ( empty( $post->post_type ) || ! in_array( $post->post_type, array( 'recipe' ) ) ) {
		return;
	}

	$GLOBALS['recipe'] = rcpm_get_the_recipe( $post->ID );
}

add_action( 'the_post', 'rcpm_setup_recipe_data' );

function setup_recipe_phase_data( $phase ) {
	unset( $GLOBALS['recipe_phase'] );

	if ( ! empty( $phase ) ) {
		$GLOBALS['recipe_phase'] = $phase;
	}

	return $GLOBALS['recipe_phase'];
}


if ( ! function_exists( 'rcpm_reset_loop' ) ) {

	/**
	 * Reset the loop's index and columns when we're done outputting a recipe loop.
	 *
	 * @subpackage    Loop
	 */
	function rcpm_reset_loop() {
		global $rcpm_loop;
		// Reset loop/columns globals when starting a new loop
		$rcpm_loop['loop'] = $rcpm_loop['columns'] = '';
	}
}
add_filter( 'loop_end', 'rcpm_reset_loop' );


/**
 * Products RSS Feed.
 *
 * @access public
 */
function rcpm_recipes_rss_feed() {
	// Product RSS
	if ( is_post_type_archive( 'recipe' ) || is_singular( 'recipe' ) ) {

		$feed = get_post_type_archive_feed_link( 'recipe' );

		echo '<link rel="alternate" type="application/rss+xml"  title="' . esc_attr__( 'New recipes', 'recipe-manager' ) . '" href="' . esc_url( $feed ) . '" />';

	} elseif ( is_tax( 'recipe_cat' ) ) {

		$term = get_term_by( 'slug', esc_attr( get_query_var( 'recipe_cat' ) ), 'recipe_cat' );

		$feed = add_query_arg( 'recipe_cat', $term->slug, get_post_type_archive_feed_link( 'recipe' ) );

		echo '<link rel="alternate" type="application/rss+xml"  title="' . esc_attr( sprintf( __( 'New recipes added to %s', 'recipe-manager' ), $term->name ) ) . '" href="' . esc_url( $feed ) . '" />';

	} elseif ( is_tax( 'recipe_tag' ) ) {

		$term = get_term_by( 'slug', esc_attr( get_query_var( 'recipe_tag' ) ), 'recipe_tag' );

		$feed = add_query_arg( 'recipe_tag', $term->slug, get_post_type_archive_feed_link( 'recipe' ) );

		echo '<link rel="alternate" type="application/rss+xml"  title="' . sprintf( __( 'New recipes tagged %s', 'recipe-manager' ), urlencode( $term->name ) ) . '" href="' . esc_url( $feed ) . '" />';

	}
}

/**
 * Output generator tag to aid debugging.
 *
 * @access public
 */
function rcpm_generator_tag( $gen, $type ) {
	switch ( $type ) {
		case 'html':
			$gen .= "\n" . '<meta name="generator" content="Recipe Manager ' . esc_attr( RCPM::$VER ) . '">';
			break;
		case 'xhtml':
			$gen .= "\n" . '<meta name="generator" content="Recipe Manager ' . esc_attr( RCPM::$VER ) . '" />';
			break;
	}

	return $gen;
}

/**
 * Add body classes for RCPM pages
 *
 * @param  array $classes
 *
 * @return array
 */
function rcpm_body_class( $classes ) {
	$classes = (array) $classes;

	if ( is_singular( 'recipe' ) ) {
		$classes[] = 'rcpm-page';
	}

	return array_unique( $classes );
}

/**
 * Display the classes for the recipe cat div.
 *
 * @since 1.0.0
 *
 * @param string|array $class One or more classes to add to the class list.
 * @param object $category object Optional.
 */
function rcpm_recipe_cat_class( $class = '', $category = null ) {
	// Separates classes with a single space, collates classes for post DIV
	echo 'class="' . esc_attr( join( ' ', rcpm_get_recipe_cat_class( $class, $category ) ) ) . '"';
}

/**
 * Get the classes for the recipe cat div.
 *
 * @since 1.0.0
 *
 * @param string|array $class One or more classes to add to the class list.
 * @param object $category object Optional.
 *
 * @return array
 */
function rcpm_get_recipe_cat_class( $class = '', $category = null ) {
	global $rcpm_loop;

	$classes   = is_array( $class ) ? $class : array_map( 'trim', explode( ' ', $class ) );
	$classes[] = 'recipe-category';
	$classes[] = 'recipe';

	if ( ( $rcpm_loop['loop'] - 1 ) % $rcpm_loop['columns'] == 0 || $rcpm_loop['columns'] == 1 ) {
		$classes[] = 'first';
	}

	if ( $rcpm_loop['loop'] % $rcpm_loop['columns'] == 0 ) {
		$classes[] = 'last';
	}

	$classes = apply_filters( 'recipe_cat_class', $classes, $class, $category );

	return array_unique( array_filter( $classes ) );
}

/**
 * Adds extra post classes for recipes
 *
 * @since 1.0.0
 *
 * @param array $classes
 * @param string|array $class
 * @param int $post_id
 *
 * @return array
 */
function rcpm_recipe_post_class( $classes, $class = '', $post_id = null ) {
	if ( ! $post_id || 'recipe' !== get_post_type( $post_id ) ) {
		return $classes;
	}

	$recipe = rcpm_get_the_recipe( $post_id );

	if ( $recipe ) {
		if ( $recipe->is_allergy_free() ) {
			$classes[] = 'allergy-free';
		}
		if ( $recipe->has_allergens() ) {
			$classes[] = 'has-allergens';
		}


		// add category slugs
		$categories = get_the_terms( $recipe->id, 'recipe_cat' );
		if ( ! empty( $categories ) ) {
			foreach ( $categories as $key => $value ) {
				$classes[] = 'recipe-cat-' . $value->slug;
			}
		}

		// add tag slugs
		$tags = get_the_terms( $recipe->id, 'recipe_tag' );
		if ( ! empty( $tags ) ) {
			foreach ( $tags as $key => $value ) {
				$classes[] = 'recipe-tag-' . $value->slug;
			}
		}

	}

	if ( false !== ( $key = array_search( 'hentry', $classes ) ) ) {
		unset( $classes[ $key ] );
	}

	return $classes;
}

/** Template pages ********************************************************/


if ( ! function_exists( 'rcpm_content' ) ) {

	/**
	 * Output RCPM content.
	 *
	 * This function is only used in the optional 'rcpm.php' template
	 * which people can add to their themes to add basic rcpm support
	 * without hooks or modifying core templates.
	 *
	 */
	function rcpm_content() {

		if ( is_singular( 'recipe' ) ) {

			while ( have_posts() ) : the_post();

				rcpm_get_template_part( 'content', 'single-recipe' );

			endwhile;

		} else { ?>

			<?php if ( apply_filters( 'rcpm_show_page_title', true ) ) : ?>

				<h1 class="page-title"><?php rcpm_page_title(); ?></h1>

			<?php endif; ?>

			<?php do_action( 'rcpm_archive_description' ); ?>

			<?php if ( have_posts() ) : ?>

				<?php do_action( 'rcpm_before_recipe_loop' ); ?>

				<?php rcpm_recipe_loop_start(); ?>

				<?php //rcpm_recipe_subcategories(); ?>

				<?php while ( have_posts() ) : the_post(); ?>

					<?php rcpm_get_template_part( 'content', 'recipe' ); ?>

				<?php endwhile; // end of the loop. ?>

				<?php rcpm_recipe_loop_end(); ?>

				<?php do_action( 'rcpm_after_recipe_loop' ); ?>

			<?php else : ?>

				<?php rcpm_get_template( 'loop/no-recipes-found.php' ); ?>

			<?php endif;

		}
	}
}

/** Global ****************************************************************/

if ( ! function_exists( 'rcpm_output_content_wrapper' ) ) {

	/**
	 * Output the start of the page wrapper.
	 *
	 */
	function rcpm_output_content_wrapper() {
		rcpm_get_template( 'global/wrapper-start.php' );
	}
}

if ( ! function_exists( 'rcpm_output_content_wrapper_end' ) ) {

	/**
	 * Output the end of the page wrapper.
	 *
	 */
	function rcpm_output_content_wrapper_end() {
		rcpm_get_template( 'global/wrapper-end.php' );
	}
}

if ( ! function_exists( 'rcpm_get_sidebar' ) ) {

	/**
	 * Get the recipes sidebar template.
	 *
	 */
	function rcpm_get_sidebar() {
		rcpm_get_template( 'global/sidebar.php' );
	}
}

/** Loop ******************************************************************/
if ( ! function_exists( 'rcpm_page_title' ) ) {

	/**
	 * rcpm_page_title function.
	 *
	 * @param  boolean $echo
	 *
	 * @return string
	 */
	function rcpm_page_title( $echo = true ) {

		if ( is_search() ) {
			$page_title = sprintf( __( 'Search Results: &ldquo;%s&rdquo;', 'recipe-manager' ), get_search_query() );

			if ( get_query_var( 'paged' ) ) {
				$page_title .= sprintf( __( '&nbsp;&ndash; Page %s', 'recipe-manager' ), get_query_var( 'paged' ) );
			}

		} elseif ( is_tax() ) {

			$page_title = single_term_title( "", false );

		} else {

			$page_title = get_the_archive_title();

			//$recipes_page_id = 1;
			//$page_title   = get_the_title( $recipes_page_id );

		}

		$page_title = apply_filters( 'rcpm_page_title', $page_title );

		if ( $echo ) {
			echo $page_title;
		} else {
			return $page_title;
		}
	}
}

if ( ! function_exists( 'rcpm_recipe_loop_start' ) ) {

	/**
	 * Output the start of a recipe loop. By default this is a UL
	 *
	 * @param bool $echo
	 *
	 * @return string
	 */
	function rcpm_recipe_loop_start( $echo = true ) {
		ob_start();
		rcpm_get_template( 'loop/loop-start.php' );
		if ( $echo ) {
			echo ob_get_clean();
		} else {
			return ob_get_clean();
		}
	}
}

if ( ! function_exists( 'rcpm_recipe_loop_end' ) ) {

	/**
	 * Output the end of a recipe loop. By default this is a UL
	 *
	 * @param bool $echo
	 *
	 * @return string
	 */
	function rcpm_recipe_loop_end( $echo = true ) {
		ob_start();

		rcpm_get_template( 'loop/loop-end.php' );

		if ( $echo ) {
			echo ob_get_clean();
		} else {
			return ob_get_clean();
		}
	}
}

if ( ! function_exists( 'rcpm_template_loop_recipe_title' ) ) {

	/**
	 * Show the recipe title in the recipe loop. By default this is an H3
	 */
	function rcpm_template_loop_recipe_title() {
		rcpm_get_template( 'loop/title.php' );
	}
}

if ( ! function_exists( 'rcpm_taxonomy_archive_description' ) ) {

	/**
	 * Show an archive description on taxonomy archives
	 *
	 * @subpackage    Archives
	 */
	function rcpm_taxonomy_archive_description() {
		if ( is_tax( array( 'recipe_cat', 'recipe_tag' ) ) && get_query_var( 'paged' ) == 0 ) {
			$description = rcpm_format_content( term_description() );
			if ( $description ) {
				echo '<div class="term-description">' . $description . '</div>';
			}
		}
	}
}

if ( ! function_exists( 'rcpm_recipe_archive_description' ) ) {

	/**
	 * Show a recipes page description on recipe archives
	 *
	 * @subpackage    Archives
	 */
	function rcpm_recipe_archive_description() {
		if ( is_post_type_archive( 'recipe' ) && get_query_var( 'paged' ) == 0 ) {
			$recipes_page = get_post( rcpm_get_page_id( 'recipes' ) );
			if ( $recipes_page ) {
				$description = rcpm_format_content( $recipes_page->post_content );
				if ( $description ) {
					echo '<div class="page-description">' . $description . '</div>';
				}
			}
		}
	}
}

if ( ! function_exists( 'rcpm_template_loop_recipe_thumbnail' ) ) {

	/**
	 * Get the recipe thumbnail for the loop.
	 *
	 * @subpackage    Loop
	 */
	function rcpm_template_loop_recipe_thumbnail() { ?>
	<a <?php rcpm_attr( 'recipe-image' ); ?> href="<?php the_permalink(); ?>">
		<?php echo rcpm_get_recipe_thumbnail(); ?>
		</a><?php
	}
}

if ( ! function_exists( 'rcpm_template_loop_recipe_time' ) ) {

	/**
	 * Get the recipe time for the loop.
	 *
	 * @subpackage    Loop
	 */
	function rcpm_template_loop_recipe_time() {
		rcpm_get_template( 'loop/time.php' );
	}
}

if ( ! function_exists( 'rcpm_template_loop_recipe_allergens' ) ) {

	/**
	 * Get the recipe allergens for the loop.
	 *
	 * @subpackage    Loop
	 */
	function rcpm_template_loop_recipe_allergens() {
		rcpm_get_template( 'loop/allergen-warnings.php' );
	}
}

if ( ! function_exists( 'rcpm_template_loop_recipe_meta' ) ) {

	/**
	 * Get the recipe meta for the loop.
	 *
	 * @subpackage    Loop
	 */
	function rcpm_template_loop_recipe_meta() {
		rcpm_get_template( 'loop/meta.php' );
	}
}

if ( ! function_exists( 'rcpm_template_loop_recipe_excerpt' ) ) {

	/**
	 * Get the recipe excerpt for the loop.
	 *
	 * @subpackage    Loop
	 */
	function rcpm_template_loop_recipe_excerpt() {
		rcpm_get_template( 'loop/excerpt.php' );
	}
}

if ( ! function_exists( 'rcpm_get_recipe_thumbnail' ) ) {

	/**
	 * Get the recipe thumbnail, or the placeholder if not set.
	 *
	 * @subpackage    Loop
	 *
	 * @param string $size (default: 'recipes_catalog')
	 * @param int $deprecated1 Deprecated since RCPM 2.0 (default: 0)
	 * @param int $deprecated2 Deprecated since RCPM 2.0 (default: 0)
	 *
	 * @return string
	 */
	function rcpm_get_recipe_thumbnail( $size = 'recipes_catalog' ) {
		global $post;

		if ( has_post_thumbnail() ) {
			return get_the_post_thumbnail( $post->ID, $size );
		} elseif ( rcpm_placeholder_img_src() ) {
			return rcpm_placeholder_img( $size );
		}
	}
}

if ( ! function_exists( 'rcpm_result_count' ) ) {

	/**
	 * Output the result count text (Showing x - x of x results).
	 *
	 * @subpackage    Loop
	 */
	function rcpm_result_count() {
		rcpm_get_template( 'loop/result-count.php' );
	}
}

if ( ! function_exists( 'rcpm_pagination' ) ) {

	/**
	 * Output the pagination.
	 *
	 * @subpackage    Loop
	 */
	function rcpm_pagination() {
		rcpm_get_template( 'loop/pagination.php' );
	}
}

/** Single Product ********************************************************/
if ( ! function_exists( 'rcpm_show_recipe_images' ) ) {

	/**
	 * Output the recipe image before the single recipe summary.
	 *
	 * @subpackage    Recipe
	 */
	function rcpm_show_recipe_images() {
		rcpm_get_template( 'single-recipe/recipe-image.php' );
	}
}

if ( ! function_exists( 'rcpm_template_single_title' ) ) {

	/**
	 * Output the recipe title.
	 *
	 * @subpackage    Recipe
	 */
	function rcpm_template_single_title() {
		rcpm_get_template( 'single-recipe/title.php' );
	}
}

if ( ! function_exists( 'rcpm_template_single_description' ) ) {

	/**
	 * Output the recipe full description (the_content).
	 *
	 * @subpackage    Recipe
	 */
	function rcpm_template_single_description() {
		rcpm_get_template( 'single-recipe/description.php' );
	}
}

if ( ! function_exists( 'rcpm_template_single_excerpt' ) ) {

	/**
	 * Output the recipe short description (excerpt).
	 *
	 * @subpackage    Recipe
	 */
	function rcpm_template_single_excerpt() {
		rcpm_get_template( 'single-recipe/short-description.php' );
	}
}

if ( ! function_exists( 'rcpm_template_single_actions_bar' ) ) {

	/**
	 * Output the recipe action bar.
	 *
	 * @subpackage    Recipe
	 */
	function rcpm_template_single_actions_bar() {
		rcpm_get_template( 'single-recipe/action-bar.php' );
	}
}

if ( ! function_exists( 'rcpm_template_single_meta' ) ) {

	/**
	 * Output the recipe meta.
	 *
	 * @subpackage    Recipe
	 */
	function rcpm_template_single_meta() {
		rcpm_get_template( 'single-recipe/meta.php' );
	}
}

if ( ! function_exists( 'rcpm_template_single_sharing' ) ) {

	/**
	 * Output the recipe sharing.
	 *
	 * @subpackage    Recipe
	 */
	function rcpm_template_single_sharing() {
		rcpm_get_template( 'single-recipe/share.php' );
	}
}

if ( ! function_exists( 'rcpm_template_single_recipe_times' ) ) {

	/**
	 * Output the recipe times.
	 *
	 * @subpackage    Recipe
	 */
	function rcpm_template_single_recipe_times() {
		rcpm_get_template( 'single-recipe/recipe-times.php' );
	}
}

if ( ! function_exists( 'rcpm_template_single_recipe_servings' ) ) {

	/**
	 * Output the recipe servings.
	 *
	 * @subpackage    Recipe
	 */
	function rcpm_template_single_recipe_servings() {
		rcpm_get_template( 'single-recipe/recipe-servings.php' );
	}
}

if ( ! function_exists( 'rcpm_template_single_recipe_phases' ) ) {

	/**
	 * Output the recipe phases.
	 *
	 * @subpackage    Recipe
	 */
	function rcpm_template_single_recipe_phases() {
		rcpm_get_template( 'single-recipe/recipe-phases.php' );
	}
}

if ( ! function_exists( 'rcpm_breadcrumb' ) ) {

	/**
	 * Output the RCPM Breadcrumb
	 */
	function rcpm_breadcrumb( $args = array() ) {
		$args = wp_parse_args( $args, apply_filters( 'rcpm_breadcrumb_defaults', array(
			'delimiter'   => '&nbsp;&#47;&nbsp;',
			'wrap_before' => '<nav class="rcpm-breadcrumb" ' . ( is_single() ? 'itemprop="breadcrumb"' : '' ) . '>',
			'wrap_after'  => '</nav>',
			'before'      => '',
			'after'       => '',
			'home'        => _x( 'Home', 'breadcrumb', 'recipe-manager' ),
		) ) );

		$breadcrumbs = new Breadcrumb();

		if ( $args['home'] ) {
			$breadcrumbs->add_crumb( $args['home'], apply_filters( 'rcpm_breadcrumb_home_url', home_url() ) );
		}

		$args['breadcrumb'] = $breadcrumbs->generate();

		rcpm_get_template( 'global/breadcrumb.php', $args );
	}
}

/** Forms ****************************************************************/
if ( ! function_exists( 'get_recipe_search_form' ) ) {

	/**
	 * Display recipe search form.
	 *
	 * Will first attempt to locate the recipe-searchform.php file in either the child or
	 * the parent, then load it. If it doesn't exist, then the default search form
	 * will be displayed.
	 *
	 * The default searchform uses html5.
	 *
	 * @subpackage    Forms
	 *
	 * @param bool $echo (default: true)
	 *
	 * @return string
	 */
	function get_recipe_search_form( $echo = true ) {
		ob_start();

		do_action( 'pre_get_recipe_search_form' );

		rcpm_get_template( 'recipe-searchform.php' );

		$form = apply_filters( 'get_recipe_search_form', ob_get_clean() );

		if ( $echo ) {
			echo $form;
		} else {
			return $form;
		}
	}
}

/** Global ****************************************************************/
if ( ! function_exists( 'rcpm_output_content_wrapper' ) ) {

	/**
	 * Output the start of the page wrapper.
	 *
	 */
	function rcpm_output_content_wrapper() {
		rcpm_get_template( 'global/wrapper-start.php' );
	}
}

if ( ! function_exists( 'rcpm_output_content_wrapper_end' ) ) {

	/**
	 * Output the end of the page wrapper.
	 *
	 */
	function rcpm_output_content_wrapper_end() {
		rcpm_get_template( 'global/wrapper-end.php' );
	}
}

if ( ! function_exists( 'rcpm_get_sidebar' ) ) {

	/**
	 * Get the recipes sidebar template.
	 *
	 */
	function rcpm_get_sidebar() {
		rcpm_get_template( 'global/sidebar.php' );
	}
}

if ( ! function_exists( 'rcpm_template_single_meta' ) ) {

	/**
	 * Output the recipe meta.
	 *
	 * @subpackage    Recipe
	 */
	function rcpm_template_single_meta() {
		rcpm_get_template( 'single-recipe/meta.php' );
	}
}

if ( ! function_exists( 'rcpm_template_single_meta_courses' ) ) {

	/**
	 * Output the recipe meta: courses.
	 *
	 * @subpackage    Recipe
	 */
	function rcpm_template_single_meta_courses() {
		rcpm_get_template( 'single-recipe/meta/courses.php' );
	}
}

if ( ! function_exists( 'rcpm_template_single_meta_categories' ) ) {

	/**
	 * Output the recipe meta: categories.
	 *
	 * @subpackage    Recipe
	 */
	function rcpm_template_single_meta_categories() {
		rcpm_get_template( 'single-recipe/meta/categories.php' );
	}
}

if ( ! function_exists( 'rcpm_template_single_meta_post_tags' ) ) {

	/**
	 * Output the recipe meta: post_tags.
	 *
	 * @subpackage    Recipe
	 */
	function rcpm_template_single_meta_post_tags() {
		rcpm_get_template( 'single-recipe/meta/post_tags.php' );
	}
}

if ( ! function_exists( 'rcpm_template_single_allergen_warnings' ) ) {

	/**
	 * Output the recipe allergen warnings.
	 *
	 * @subpackage    Recipe
	 */
	function rcpm_template_single_allergen_warnings() {
		rcpm_get_template( 'single-recipe/allergen-warnings.php' );
	}
}

if ( ! function_exists( 'rcpm_template_single_additional_notes' ) ) {

	/**
	 * Output the recipe additional notes.
	 *
	 * @subpackage    Recipe
	 */
	function rcpm_template_single_additional_notes() {
		rcpm_get_template( 'single-recipe/additional-notes.php' );
	}
}

if ( ! function_exists( 'rcpm_placeholder_img_src' ) ) {
	/**
	 * Output the recipe additional notes.
	 *
	 * @subpackage    Recipe
	 */
	function rcpm_placeholder_img_src() {
		return apply_filters( 'rcpm_placeholder_img_src', '//lorempixel.com/output/food-q-c-150-150-3.jpg' );
	}
}

if ( ! function_exists( 'rcpm_placeholder_img' ) ) {
	/**
	 * Output the recipe additional notes.
	 *
	 * @subpackage    Recipe
	 */
	function rcpm_placeholder_img() {
		rcpm_get_template( 'single-recipe/recipe-image-placeholder.php' );
	}
}

function rcpm_recipe_labels( $key ) {

	$labels = apply_filters( 'rcpm_recipe_labels', array(
		'prep'             => __( 'Prep', 'recipe-manager' ),
		'cook'             => __( 'Cook', 'recipe-manager' ),
		'total'            => __( 'Total', 'recipe-manager' ),
		'servings'         => __( 'Serves', 'recipe-manager' ),
		'ingredients'      => __( 'Ingredients', 'recipe-manager' ),
		'steps'            => __( 'Instructions', 'recipe-manager' ),
		'additional_notes' => __( 'Notes', 'recipe-manager' ),
		'courses'          => __( 'Courses', 'recipe-manager' ),
		'categories'       => __( 'Categories', 'recipe-manager' ),
		'post_tags'        => __( 'Tags', 'recipe-manager' ),
	) );

	if ( ! isset( $labels[ $key ] ) ) {
		return '';
	}

	return apply_filters( "rcpm_recipe_{$key}_label", $labels[ $key ] );
}

/**
 * Display recipe phases with ingredients & steps.
 *
 * @since 1.0.0
 *
 * @param int $recipe_id
 */
function rcpm_recipe_ingredients( $recipe_id = 0 ) {
	if ( ! ( $recipe = rcpm_get_the_recipe( $recipe_id ) ) ) {
		return;
	}

	$display_ingredient_list = rcpm_get_option( 'display_ingredient_list', 'per_phase' );

	if ( $display_ingredient_list != 'per_phase' ) {
		do_action( 'rcpm_recipe_ingredients_' . $display_ingredient_list, $recipe->ID );
	}
}

add_action( 'rcpm_recipe_ingredients_together', 'rcpm_recipe_ingredients_together' );
function rcpm_recipe_ingredients_together( $recipe_id = 0 ) {
	if ( ! ( $recipe = rcpm_get_the_recipe( $recipe_id ) ) ) {
		return;
	}

	?>

	<div <?php rcpm_attr( 'recipe-ingredients-together' ); ?>>

		<h3 <?php rcpm_attr( 'recipe-ingredient-heading' ); ?>>
			<?php echo rcpm_recipe_labels( 'ingredients' ); ?>
		</h3>

		<ul <?php rcpm_attr( 'recipe-ingredients' ); ?>>
			<?php foreach ( $recipe->get_all_ingredients() as $ingredient ) : ?>

				<li <?php rcpm_attr( 'recipe-ingredient' ); ?>>

					<?php printf( '%s %s %s', $ingredient['measure'], $ingredient['unit'], $ingredient['label'] ); ?>

					<?php if ( $ingredient['note'] != '' ) : ?>
						<span <?php rcpm_attr( 'recipe-ingredient-note' ); ?>>
							<?php esc_html_e( $ingredient['note'] ); ?>
						</span>
					<?php endif; ?>

				</li>

			<?php endforeach; ?>
		</ul>

	</div>

	<?php

}

add_action( 'rcpm_recipe_ingredients_together_by_phase', 'rcpm_recipe_ingredients_together_by_phase' );
function rcpm_recipe_ingredients_together_by_phase( $recipe_id = 0 ) {
	if ( ! ( $recipe = rcpm_get_the_recipe( $recipe_id ) ) ) {
		return;
	} ?>

	<div <?php rcpm_attr( 'recipe-ingredients-together-by-phase' ); ?>>

		<h3 <?php rcpm_attr( 'recipe-ingredient-heading' ); ?>>
			<?php echo rcpm_recipe_labels( 'ingredients' ); ?>
		</h3>

		<?php foreach ( $recipe->get_phases() as $index => $phase ) : ?>

			<div <?php rcpm_attr( 'recipe-ingredient-phase' ); ?>>

				<?php if ( $phase['name'] != '' ) : ?>
					<strong <?php rcpm_attr( 'recipe-ingredients-phase-name' ); ?>>
						<?php _e( $phase['name'] ); ?>
					</strong>
				<?php endif; ?>

				<ul <?php rcpm_attr( 'recipe-ingredients' ); ?>>

					<?php foreach ( $phase['ingredients'] as $ingredient ) : ?>
						<li <?php rcpm_attr( 'recipe-ingredient' ); ?>>

							<?php printf( '%s %s %s', $ingredient['measure'], $ingredient['unit'], $ingredient['label'] ); ?>

							<?php if ( $ingredient['note'] != '' ) : ?>

								<span <?php rcpm_attr( 'recipe-ingredient-note' ); ?>>
							<?php esc_html_e( $ingredient['note'] ); ?>
						</span>

							<?php endif; ?>

						</li>
					<?php endforeach; ?>

				</ul>

			</div>

		<?php endforeach; ?>

	</div>

	<?php
}
