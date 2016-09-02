<?php
/**
 * Copyright: 2015 Daniel Iser
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

// Exit if accessed directly
namespace RCPM\Partner_Tables;

use RCPM\Ingredient;
use RCPM\Recipe;
use RCPM\Partner_Table;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Recipe_Ingredients extends Partner_Table {

	public $post_types = array( 'recipe' );
	public $name = 'recipe_ingredients';
	public $version = '1.0.0';
	public $primary_key = 'id';
	public $foreign_key = 'recipe_id';
	public $auto_join = true;

	/**
	 * Get columns and formats
	 *
	 * @access  public
	 * @since   1.0.0
	 */
	public function get_columns() {
		return array(
			'id'            => '%d',
			'recipe_id'     => '%d',
			'ingredient_id' => '%d',
		);
	}

	/**
	 * Get default column values
	 *
	 * @access  public
	 * @since   1.0.0
	 */
	public function get_column_defaults() {
		return array(
			'recipe_id'     => 0,
			'ingredient_id' => 0,
		);
	}

	/**
	 * Install/update the db table schema.
	 *
	 * @access  public
	 * @since   1.0.0
	 */
	public function install() {
		global $wpdb, $EZSQL_ERROR;

		$current_version = get_option( $this->table_name . '_db_version', false );

		if ( ! $current_version || version_compare( $current_version, $this->version, '<' ) ) {

			$charset_collate = $wpdb->get_charset_collate();

			$sql = "CREATE TABLE {$this->table_name} (
			  `recipe_id` bigint(20) unsigned NOT NULL,
			  `ingredient_id` bigint(20) unsigned NOT NULL,
			  PRIMARY KEY (`recipe_id`,`ingredient_id`),
			  KEY `ingredient_id` (`ingredient_id`)
			) {$charset_collate};";

			dbDelta( $sql );

			update_option( $this->table_name . '_db_version', $this->version );

			// Remove faulty EZSQL DESCRIBE error for non existent tables.
			$last_error_key = count( $EZSQL_ERROR ) - 1;
			if ( $EZSQL_ERROR[ $last_error_key ]['query'] == "DESCRIBE $this->table_name;" ) {
				unset( $EZSQL_ERROR[ $last_error_key ] );
			}
		}
	}

	public static function activation_install( $network_wide = false ) {
		global $wpdb;

		$class = new self;

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		if ( is_multisite() && $network_wide ) {
			// store the current blog id
			// Get all blogs in the network and activate plugin on each one
			$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
			foreach ( $blog_ids as $blog_id ) {
				switch_to_blog( $blog_id );
				$class->install();
				restore_current_blog();
			}
		} else {
			$class->install();
		}
	}

	/**
	 * Get a list of ingredients for a recipe.
	 *
	 * @access  public
	 * @since   1.0.0
	 *
	 * @param $recipe_id
	 *
	 * @return array
	 */
	public function get_ingredients( $recipe_id ) {
		global $wpdb;
		$ingredient_ids = $wpdb->get_var( $wpdb->prepare( "SELECT GROUP_CONCAT( ingredient_id ) as ingredients FROM $this->table_name WHERE recipe_id = %d", $recipe_id ) );

		if ( is_wp_error( $ingredient_ids ) || empty( $ingredient_ids ) ) {
			return array();
		}

		$ingredient_ids = explode( ',', $ingredient_ids );
		$query_ids      = $ingredient_ids;

		foreach ( $query_ids as $key => $id ) {
			if ( wp_cache_get( $id, 'posts' ) ) {
				unset( $query_ids[ $key ] );
			}
		}

		if ( count( $query_ids ) > 0 ) {
			$ids = implode( ',', $query_ids );

			$rows = $wpdb->get_results( "SELECT * FROM $wpdb->posts WHERE ID IN ($ids) AND post_type = 'ingredient'" );

			if ( is_wp_error( $rows ) || empty( $rows ) ) {
				return array();
			}

			foreach ( $rows as $row ) {
				$row = sanitize_post( $row, 'raw' );
				wp_cache_add( $row->ID, $row, 'posts' );
			}

			update_meta_cache( 'post', $query_ids );
		}

		$ingredients = array();

		foreach ( $ingredient_ids as $id ) {
			$ingredients[] = new Ingredient( $id );
		}

		return $ingredients;
	}

	/**
	 * Get a list of recipes for an ingredient.
	 *
	 * @access  public
	 * @since   1.0.0
	 *
	 * @param $ingredient_id
	 *
	 * @return array
	 */
	public function get_recipes( $ingredient_id ) {
		global $wpdb;
		$recipe_ids = $wpdb->get_var( $wpdb->prepare( "SELECT GROUP_CONCAT( recipe_id ) as recipes FROM $this->table_name WHERE ingredient_id = %d", $ingredient_id ) );

		if ( is_wp_error( $recipe_ids ) || empty( $recipe_ids ) ) {
			return array();
		}

		$recipe_ids = explode( ',', $recipe_ids );
		$query_ids  = $recipe_ids;

		foreach ( $query_ids as $key => $id ) {
			if ( wp_cache_get( $id, 'posts' ) ) {
				unset( $query_ids[ $key ] );
			}
		}

		if ( count( $query_ids ) > 0 ) {
			$ids = implode( ',', $query_ids );

			$rows = $wpdb->get_results( "SELECT * FROM $wpdb->posts WHERE ID IN ($ids) AND post_type = 'recipe'" );

			if ( is_wp_error( $rows ) || empty( $rows ) ) {
				return array();
			}

			foreach ( $rows as $row ) {
				$row = sanitize_post( $row, 'raw' );
				wp_cache_add( $row->ID, $row, 'posts' );
			}

			update_meta_cache( 'post', $query_ids );
		}

		$recipes = array();

		foreach ( $recipe_ids as $id ) {
			$recipes[] = new Recipe( $id );
		}

		return $recipes;
	}

	/**
	 * Insert a list of ingredients for a recipe.
	 *
	 * @access  public
	 * @since   1.0.0
	 *
	 * @param $recipe_id
	 * @param $ingredient_ids
	 *
	 * @return mixed
	 */
	public function insert_ingredient_list( $recipe_id, $ingredient_ids ) {
		global $wpdb;

		$inserts = array();

		foreach ( $ingredient_ids as $id ) {
			$inserts[] = "('$recipe_id', '$id')";
		}

		$values = implode( ',', $inserts );

		return $wpdb->query( "INSERT INTO $this->table_name (recipe_id, ingredient_id) VALUES $values;" );
	}

}
