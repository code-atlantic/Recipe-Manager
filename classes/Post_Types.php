<?php

// Exit if accessed directly
namespace RCPM;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class RCPM\Post_Types
 */
class Post_Types {

	/**
	 * Hook the initialize method to the WP init action.
	 *
	 * @since 1.0.0
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'initialize' ) );
		add_action( 'init', array( __CLASS__, 'flush_rewrite_rules' ) );
	}

	/**
	 * Initialize post types & taxonomies.
	 *
	 * @since 1.0.0
	 */
	public static function initialize() {
		self::register_post_types();
		self::register_taxonomies();
	}

	/**
	 * Register Recipe Manager core post types.
	 *
	 * @since 1.0.0
	 */
	public static function register_post_types() {
		global $recipe_post_type, $ingredient_post_type;

		if ( ! post_type_exists( 'recipe' ) ) {

			$recipe_labels = apply_filters( 'rcpm_recipe_labels', array(
				'name'               => _x( '%2$s', 'Recipe Post Type General Name', 'recipe-manager' ),
				'singular_name'      => _x( '%1$s', 'Recipe Post Type Singular Name', 'recipe-manager' ),
				'add_new'            => __( 'Add New', 'recipe-manager' ),
				'add_new_item'       => __( 'Add New %1$s', 'recipe-manager' ),
				'edit_item'          => __( 'Edit %1$s', 'recipe-manager' ),
				'new_item'           => __( 'New %1$s', 'recipe-manager' ),
				'all_items'          => __( 'All %2$s', 'recipe-manager' ),
				'view_item'          => __( 'View %1$s', 'recipe-manager' ),
				'search_items'       => __( 'Search %2$s', 'recipe-manager' ),
				'not_found'          => __( 'No %2$s found', 'recipe-manager' ),
				'not_found_in_trash' => __( 'No %2$s found in Trash', 'recipe-manager' ),
				'parent_item_colon'  => '',
				'menu_name'          => '%2$s',
			) );

			foreach ( $recipe_labels as $key => $value ) {
				$recipe_labels[ $key ] = sprintf( $value, self::get_singular_label( 'recipe' ), self::get_plural_label( 'recipe' ) );
			}

			$public_recipes = function_exists( 'rcpm_get_option' ) ? ! rcpm_get_option( 'disable_public_recipes', false ) : true;

			$recipe_args = apply_filters( 'rcpm_recipe_post_type_args', array(
				'labels'      => $recipe_labels,
				'description' => __( 'Recipe information pages', 'recipe-manager' ),
				'menu_icon'   => 'dashicons-clock',
				'supports'    => apply_filters( 'rcpm_recipe_supports', array(
					'title',
					'editor',
					'revisions',
					'author',
					'thumbnail',
					'excerpt',
					'comments',
					'revisions',
				) ),
				'taxonomies'  => array( 'course' ),
				'public'      => $public_recipes,
				'show_ui'     => true,
			) );

			if ( $public_recipes ) {
				$recipe_args = array_merge_recursive( $recipe_args, array(
					'has_archive' => true,
					'rewrite'     => array(
						'slug'       => 'recipes',
						'with_front' => false,
						'pages'      => true,
						'feeds'      => true,
					),
				) );
			}

			$recipe_post_type = register_post_type( 'recipe', $recipe_args );

		}

		if ( ! post_type_exists( 'ingredient' ) ) {

			$ingredient_labels = apply_filters( 'rcpm_ingredient_labels', array(
				'name'               => _x( '%2$s', 'Ingredient Post Type General Name', 'recipe-manager' ),
				'singular_name'      => _x( '%1$s', 'Ingredient Post Type Singular Name', 'recipe-manager' ),
				'add_new'            => __( 'Add New', 'recipe-manager' ),
				'add_new_item'       => __( 'Add New %1$s', 'recipe-manager' ),
				'edit_item'          => __( 'Edit %1$s', 'recipe-manager' ),
				'new_item'           => __( 'New %1$s', 'recipe-manager' ),
				'all_items'          => __( '%2$s', 'recipe-manager' ),
				'view_item'          => __( 'View %1$s', 'recipe-manager' ),
				'search_items'       => __( 'Search %2$s', 'recipe-manager' ),
				'not_found'          => __( 'No %2$s found', 'recipe-manager' ),
				'not_found_in_trash' => __( 'No %2$s found in Trash', 'recipe-manager' ),
				'parent_item_colon'  => '',
				'menu_name'          => '%2$s',
			) );

			foreach ( $ingredient_labels as $key => $value ) {
				$ingredient_labels[ $key ] = sprintf( $value, self::get_singular_label( 'ingredient' ), self::get_plural_label( 'ingredient' ) );
			}

			$public_ingredients = function_exists( 'rcpm_get_option' ) ? rcpm_get_option( 'public_ingredients', false ) : false;

			$ingredient_args = apply_filters( 'rcpm_ingredient_post_type_args', array(
				'labels'      => $ingredient_labels,
				'description' => __( 'Ingredient information pages', 'recipe-manager' ),
				'menu_icon'   => 'dashicons-carrot',
				'supports'    => apply_filters( 'rcpm_ingredient_supports', array(
					'title',
					'editor',
					'thumbnail',
					'excerpt',
					'revisions',
					'author',
					'comments',
				) ),
				'public'      => $public_ingredients,
				'show_ui'     => true,

			) );

			if ( $public_ingredients ) {
				$recipe_args = array_merge_recursive( $ingredient_args, array(
					'has_archive' => true,
					'rewrite'     => array(
						'slug'       => 'ingredients',
						'with_front' => false,
						'pages'      => true,
						'feeds'      => true,
					),
				) );
			}

			$ingredient_post_type = register_post_type( 'ingredient', $ingredient_args );
		}

	}

	/**
	 * Register Recipe Manager core taxonomies
	 *
	 * @since 1.0.0
	 */
	public static function register_taxonomies() {

		if ( ! taxonomy_exists( 'recipe_course' ) ) {
			$course_labels = apply_filters( 'rcpm_recipe_course_labels', array(
				'name'                       => _x( "Courses", 'Taxonomy General Name', 'recipe-manager' ),
				'singular_name'              => _x( "Course", 'Taxonomy Singular Name', 'recipe-manager' ),
				'menu_name'                  => __( "Course", 'recipe-manager' ),
				'all_items'                  => __( "All Courses", 'recipe-manager' ),
				'parent_item'                => __( "Parent Course", 'recipe-manager' ),
				'parent_item_colon'          => __( "Parent Course:", 'recipe-manager' ),
				'new_item_name'              => __( "New Course Name", 'recipe-manager' ),
				'add_new_item'               => __( "Add New Course", 'recipe-manager' ),
				'edit_item'                  => __( "Edit Course", 'recipe-manager' ),
				'update_item'                => __( "Update Course", 'recipe-manager' ),
				'separate_items_with_commas' => __( "Separate courses with commas", 'recipe-manager' ),
				'search_items'               => __( "Search courses", 'recipe-manager' ),
				'add_or_remove_items'        => __( "Add or remove courses", 'recipe-manager' ),
				'choose_from_most_used'      => __( "Choose from the most used courses", 'recipe-manager' ),
			) );

			$course_args = apply_filters( 'rcpm_recipe_course_taxonomy_args', array(
				'labels'  => $course_labels,
				'public'  => true,
				'show_ui' => true,
				'rewrite' => array(
					'slug'       => 'course',
					'with_front' => false,
				),
			) );

			register_taxonomy( 'recipe_course', array( 'recipe' ), $course_args );
			register_taxonomy_for_object_type( 'recipe_course', 'recipe' );
		}


		if ( rcpm_get_option( 'use_categories' ) ) {
			register_taxonomy_for_object_type( 'category', 'recipe' );
		}

		if ( rcpm_get_option( 'use_tags' ) ) {
			register_taxonomy_for_object_type( 'post_tag', 'recipe' );
		}

	}

	/**
	 * Get Singular Label
	 *
	 * @since 1.0.0
	 *
	 * @param string $post_type
	 * @param bool $lowercase
	 *
	 * @return string $defaults['singular'] Singular label
	 */
	public static function get_singular_label( $post_type = 'recipe', $lowercase = false ) {
		$defaults = self::get_default_labels( $post_type );

		return $lowercase ? strtolower( $defaults['singular'] ) : $defaults['singular'];
	}

	/**
	 * Get Plural Label
	 *
	 * @since 1.0.0
	 *
	 * @param string $post_type
	 * @param bool $lowercase
	 *
	 * @return string $defaults['plural'] Plural label
	 */
	public static function get_plural_label( $post_type = 'recipe', $lowercase = false ) {
		$defaults = self::get_default_labels( $post_type );

		return $lowercase ? strtolower( $defaults['plural'] ) : $defaults['plural'];
	}

	/**
	 * Get Default Labels
	 *
	 * @since 1.0.0
	 *
	 * @param string $post_type
	 *
	 * @return array $defaults Default labels
	 */
	public static function get_default_labels( $post_type = 'recipe' ) {
		$defaults = apply_filters( 'rcpm_default_post_type_labels', array(
			'recipe'     => array(
				'singular' => __( 'Recipe', 'recipe-manager' ),
				'plural'   => __( 'Recipes', 'recipe-manager' ),
			),
			'ingredient' => array(
				'singular' => __( 'Ingredient', 'recipe-manager' ),
				'plural'   => __( 'Ingredients', 'recipe-manager' ),
			),
		) );

		return isset( $defaults[ $post_type ] ) ? $defaults[ $post_type ] : $defaults['recipe'];
	}

	public static function flush_rewrite_rules() {
		if ( get_transient( 'rcpm_flush_rewrite_rules' ) ) {
			flush_rewrite_rules();
			delete_transient( 'rcpm_flush_rewrite_rules' );
		}
	}

}

