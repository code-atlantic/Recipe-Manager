<?php
/**
 * Register Settings
 *
 * @package     Recipe Manager
 * @subpackage  Admin/Settings
 * @copyright   Copyright (c) 2015, Daniel Iser
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Get an option
 *
 * Looks to see if the specified setting exists, returns default if not
 *
 * @since 1.0.0
 * @return mixed
 */
function rcpm_get_option( $key = '', $default = false ) {
	global $rcpm_options;
	$value = isset( $rcpm_options[ $key ] ) ? $rcpm_options[ $key ] : $default;
	$value = apply_filters( 'rcpm_get_option', $value, $key, $default );

	return apply_filters( 'rcpm_get_option_' . $key, $value, $key, $default );
}

/**
 * Update an option
 *
 * Updates an rcpm setting value in both the db and the global variable.
 * Warning: Passing in an empty, false or null string value will remove
 *          the key from the rcpm_options array.
 *
 * @since 1.0.0
 *
 * @param string $key The Key to update
 * @param string|bool|int $value The value to set the key to
 *
 * @return boolean True if updated, false if not.
 */
function rcpm_update_option( $key = '', $value = false ) {

	// If no key, exit
	if ( empty( $key ) ) {
		return false;
	}

	if ( empty( $value ) ) {
		$remove_option = rcpm_delete_option( $key );

		return $remove_option;
	}

	// First let's grab the current settings
	$options = get_option( 'rcpm_settings' );

	// Let's let devs alter that value coming in
	$value = apply_filters( 'rcpm_update_option', $value, $key );

	// Next let's try to update the value
	$options[ $key ] = $value;
	$did_update      = update_option( 'rcpm_settings', $options );

	// If it updated, let's update the global variable
	if ( $did_update ) {
		global $rcpm_options;
		$rcpm_options[ $key ] = $value;

	}

	return $did_update;
}

/**
 * Remove an option
 *
 * Removes an rcpm setting value in both the db and the global variable.
 *
 * @since 1.0.0
 *
 * @param string $key The Key to delete
 *
 * @return boolean True if updated, false if not.
 */
function rcpm_delete_option( $key = '' ) {

	// If no key, exit
	if ( empty( $key ) ) {
		return false;
	}

	// First let's grab the current settings
	$options = get_option( 'rcpm_settings' );

	// Next let's try to update the value
	if ( isset( $options[ $key ] ) ) {

		unset( $options[ $key ] );

	}

	$did_update = update_option( 'rcpm_settings', $options );

	// If it updated, let's update the global variable
	if ( $did_update ) {
		global $rcpm_options;
		$rcpm_options = $options;
	}

	return $did_update;
}

/**
 * Get Settings
 *
 * Retrieves all plugin settings
 *
 * @since 1.0.0
 * @return array Recipe Manager settings
 */
function rcpm_get_settings() {
	$settings = get_option( 'rcpm_settings' );
	if ( ! is_array( $settings ) ) {
		$settings = array();
	}

	return apply_filters( 'rcpm_get_settings', $settings );
}

/**
 * Add all settings sections and fields
 *
 * @since 1.0.0
 * @return void
 */
function rcpm_register_settings() {

	if ( false == get_option( 'rcpm_settings' ) ) {
		add_option( 'rcpm_settings' );
	}

	foreach ( rcpm_get_registered_settings() as $tab => $settings ) {

		add_settings_section( 'rcpm_settings_' . $tab, __return_null(), '__return_false', 'rcpm_settings_' . $tab );

		foreach ( $settings as $option ) {

			$name = isset( $option['name'] ) ? $option['name'] : '';

			add_settings_field( 'rcpm_settings[' . $option['id'] . ']', $name, function_exists( 'rcpm_' . $option['type'] . '_callback' ) ? 'rcpm_' . $option['type'] . '_callback' : 'rcpm_missing_callback', 'rcpm_settings_' . $tab, 'rcpm_settings_' . $tab, array(
				'section'     => $tab,
				'id'          => isset( $option['id'] ) ? $option['id'] : null,
				'desc'        => ! empty( $option['desc'] ) ? $option['desc'] : '',
				'name'        => isset( $option['name'] ) ? $option['name'] : null,
				'size'        => isset( $option['size'] ) ? $option['size'] : null,
				'options'     => isset( $option['options'] ) ? $option['options'] : '',
				'std'         => isset( $option['std'] ) ? $option['std'] : '',
				'min'         => isset( $option['min'] ) ? $option['min'] : null,
				'max'         => isset( $option['max'] ) ? $option['max'] : null,
				'step'        => isset( $option['step'] ) ? $option['step'] : null,
				'chosen'      => isset( $option['chosen'] ) ? $option['chosen'] : null,
				'placeholder' => isset( $option['placeholder'] ) ? $option['placeholder'] : null,
				'allow_blank' => isset( $option['allow_blank'] ) ? $option['allow_blank'] : true,
				'readonly'    => isset( $option['readonly'] ) ? $option['readonly'] : false,
			) );
		}

	}

	// Creates our settings in the options table
	register_setting( 'rcpm_settings', 'rcpm_settings', 'rcpm_settings_sanitize' );

}

add_action( 'admin_init', 'rcpm_register_settings' );

/**
 * Retrieve the array of plugin settings
 *
 * @since 1.0.0
 * @return array
 */
function rcpm_get_registered_settings() {

	/**
	 * 'Whitelisted' Recipe Manager settings, filters are provided for each settings
	 * section to allow addons and other plugins to add their own settings
	 */
	$rcpm_settings = array(
		/** General Settings */
		'general'  => apply_filters( 'rcpm_settings_general', array(
			'disable_public_recipes' => array(
				'id'   => 'disable_public_recipes',
				'name' => __( 'Make recipes private?', 'recipe-manager' ),
				'desc' => __( 'Use this to disable direct access to recipes. Use the [recipe] shortcode instead.', 'recipe-manager' ),
				'type' => 'checkbox',
			),
		) ),
		'recipes'  => apply_filters( 'rcpm_settings_recipes', array(
			'use_categories' => array(
				'id'   => 'use_categories',
				'name' => __( 'Enable Categories?', 'recipe-manager' ),
				'desc' => __( 'This will let you add post categories to your recipes.', 'recipe-manager' ),
				'type' => 'checkbox',
			),
			'use_tags'       => array(
				'id'   => 'use_tags',
				'name' => __( 'Enable Tags?', 'recipe-manager' ),
				'desc' => __( 'This will let you add post tags to your recipes.', 'recipe-manager' ),
				'type' => 'checkbox',
			),
		) ),
		'layout'   => apply_filters( 'rcpm_settings_layout', array(
			'recipe_layout_settings'  => array(
				'id'   => 'recipe_layout_settings',
				'name' => '<strong>' . __( 'Single Recipes', 'recipe-manager' ) . '</strong>',
				'desc' => '',
				'type' => 'header',
			),
			'featured_image_size'     => array(
				'id'      => 'featured_image_size',
				'name'    => __( 'Featured Image Size', 'recipe-manager' ),
				'desc'    => __( 'This only applies to recipe pages, not the shortcode.', 'recipe-manager' ),
				'type'    => 'select',
				'options' => array(
					'thumbnail' => __( 'Thumbnail', 'recipe-manager' ),
					'medium'    => __( 'Medium', 'recipe-manager' ),
					'large'     => __( 'Large', 'recipe-manager' ),
					'full'      => __( 'Full Size', 'recipe-manager' ),
				),
				'std'     => 'large',
			),
			'featured_image_position' => array(
				'id'      => 'featured_image_position',
				'name'    => __( 'Featured Image Position', 'recipe-manager' ),
				'desc'    => __( 'Where should the image be displayed?', 'recipe-manager' ),
				'type'    => 'select',
				'options' => array(
					0 => __( 'Before The Title', 'recipe-manager' ),
					10 => __( 'After the Title', 'recipe-manager' ),
				),
				'std'     => 20,
			),
			'display_ingredient_list' => array(
				'id'      => 'display_ingredient_list',
				'name'    => __( 'Display Ingredients', 'recipe-manager' ),
				'desc'    => __( 'Choose how you want to display your ingredients on your recipe cards.', 'recipe-manager' ),
				'type'    => 'select',
				'options' => array(
					'per_phase'         => __( 'Per Phase', 'recipe-manager' ),
					'together'          => __( 'Together', 'recipe-manager' ),
					'together_by_phase' => __( 'Together Grouped by Phase', 'recipe-manager' ),
				),
				'std'     => 'per_phase',
			),
		) ),
		/** Styles Settings */
		'styles'   => apply_filters( 'rcpm_settings_styles', array(
			'disable_styles' => array(
				'id'   => 'disable_styles',
				'name' => __( 'Disable Styles', 'recipe-manager' ),
				'desc' => __( 'Check this to disable all included styling of buttons, ingredients, and all other elements.', 'recipe-manager' ),
				'type' => 'checkbox',
			),
		) ),
		/** Addon Settings */
		'addons'   => apply_filters( 'rcpm_settings_addons', array() ),
		'licenses' => apply_filters( 'rcpm_settings_licenses', array() ),
		/** Misc Settings */
		'misc'     => apply_filters( 'rcpm_settings_misc', array() ),
	);

	return apply_filters( 'rcpm_registered_settings', $rcpm_settings );
}

/**
 * Settings Sanitization
 *
 * Adds a settings error (for the updated message)
 * At some point this will validate input
 *
 * @since 1.0.0
 *
 * @param array $input The value inputted in the field
 *
 * @return string $input Sanitizied value
 */
function rcpm_settings_sanitize( $input = array() ) {

	global $rcpm_options;

	if ( empty( $_POST['_wp_http_referer'] ) ) {
		return $input;
	}

	do_action( 'rcpm_settings_sanitize' );

	parse_str( $_POST['_wp_http_referer'], $referrer );

	$settings = rcpm_get_registered_settings();
	$tab      = isset( $referrer['tab'] ) ? $referrer['tab'] : false;

	if ( ! $tab ) {
		foreach ( $settings as $key => $value ) {
			if ( ! empty( $value ) ) {
				$tab = $key;
				break;
			}
		}
	}

	$input = $input ? $input : array();
	$input = apply_filters( 'rcpm_settings_' . $tab . '_sanitize', $input );

	// Loop through each setting being saved and pass it through a sanitization filter
	foreach ( $input as $key => $value ) {

		// Get the setting type (checkbox, select, etc)
		$type = isset( $settings[ $tab ][ $key ]['type'] ) ? $settings[ $tab ][ $key ]['type'] : false;

		if ( $type ) {
			// Field type specific filter
			$input[ $key ] = apply_filters( 'rcpm_settings_sanitize_' . $type, $value, $key );
		}

		// General filter
		$input[ $key ] = apply_filters( 'rcpm_settings_sanitize', $input[ $key ], $key );
	}

	// Loop through the whitelist and unset any that are empty for the tab being saved
	if ( ! empty( $settings[ $tab ] ) ) {
		foreach ( $settings[ $tab ] as $key => $value ) {

			// settings used to have numeric keys, now they have keys that match the option ID. This ensures both methods work
			if ( is_numeric( $key ) ) {
				$key = $value['id'];
			}

			if ( empty( $input[ $key ] ) ) {
				unset( $rcpm_options[ $key ] );
			}

		}
	}

	// Merge our new settings with the existing
	$output = array_merge( $rcpm_options, $input );

	add_settings_error( 'rcpm-notices', '', __( 'Settings updated.', 'recipe-manager' ), 'updated' );

	return $output;
}

/**
 * Sanitize text fields
 *
 * @since 1.0.0
 *
 * @param array $input The field value
 *
 * @return string $input Sanitizied value
 */
function rcpm_sanitize_text_field( $input ) {
	return trim( $input );
}

add_filter( 'rcpm_settings_sanitize_text', 'rcpm_sanitize_text_field' );


/**
 * Sanitize text fields
 *
 * @since 1.0.0
 *
 * @param array $input The field value
 *
 * @return string $input Sanitizied value
 */
function rcpm_sanitize_post_type_change( $input ) {

	if ( rcpm_get_option( 'disable_public_recipes', false ) && empty( $input['disable_public_recipes'] ) ) {
		set_transient( 'rcpm_flush_rewrite_rules', true );
	} elseif ( ! rcpm_get_option( 'disable_public_recipes', false ) && isset( $input['disable_public_recipes'] ) ) {
		set_transient( 'rcpm_flush_rewrite_rules', true );
	}

	return $input;
}

add_filter( 'rcpm_settings_general_sanitize', 'rcpm_sanitize_post_type_change' );

/**
 * Retrieve settings tabs
 *
 * @since 1.0.0
 * @return array $tabs
 */
function rcpm_get_settings_tabs() {
	$settings = rcpm_get_registered_settings();

	$tabs = apply_filters( 'rcpm_settings_tabs', array(
		'general'  => __( 'General', 'recipe-manager' ),
		'recipes'  => __( 'Recipes', 'recipe-manager' ),
		'layout'   => __( 'Layouts', 'recipe-manager' ),
		'styles'   => __( 'Styles', 'recipe-manager' ),
		'addons'   => __( 'Addons', 'recipe-manager' ),
		'licenses' => __( 'Licenses', 'recipe-manager' ),
		'misc'     => __( 'Misc', 'recipe-manager' ),
	) );

	foreach ( $tabs as $key => $label ) {
		if ( empty( $settings[ $key ] ) ) {
			unset( $tabs[ $key ] );
		}
	}

	return $tabs;
}

/**
 * Retrieve a list of all published pages
 *
 * On large sites this can be expensive, so only load if on the settings page or $force is set to true
 *
 * @since 1.0.0
 *
 * @param bool $force Force the pages to be loaded even if not on settings
 *
 * @return array $pages_options An array of the pages
 */
function rcpm_get_pages( $force = false ) {

	$pages_options = array( '' => '' ); // Blank option

	if ( ( ! isset( $_GET['page'] ) || 'rcpm-settings' != $_GET['page'] ) && ! $force ) {
		return $pages_options;
	}

	$pages = get_pages();
	if ( $pages ) {
		foreach ( $pages as $page ) {
			$pages_options[ $page->ID ] = $page->post_title;
		}
	}

	return $pages_options;
}

/**
 * Header Callback
 *
 * Renders the header.
 *
 * @since 1.0.0
 *
 * @param array $args Arguments passed by the setting
 *
 * @return void
 */
function rcpm_header_callback( $args ) {
	echo '<hr/>';
}

/**
 * Checkbox Callback
 *
 * Renders checkboxes.
 *
 * @since 1.0.0
 *
 * @param array $args Arguments passed by the setting
 *
 * @global $rcpm_options Array of all the Recipe Manager Options
 * @return void
 */
function rcpm_checkbox_callback( $args ) {
	global $rcpm_options;

	$checked = isset( $rcpm_options[ $args['id'] ] ) ? checked( 1, $rcpm_options[ $args['id'] ], false ) : '';
	$html    = '<input type="checkbox" id="rcpm_settings_' . $args['id'] . '" name="rcpm_settings[' . $args['id'] . ']" value="1" ' . $checked . '/>';
	$html .= '<label class="field-description" for="rcpm_settings_' . $args['id'] . '"> ' . $args['desc'] . '</label>';

	echo $html;
}

/**
 * Multicheck Callback
 *
 * Renders multiple checkboxes.
 *
 * @since 1.0.0
 *
 * @param array $args Arguments passed by the setting
 *
 * @global $rcpm_options Array of all the Recipe Manager Options
 * @return void
 */
function rcpm_multicheck_callback( $args ) {
	global $rcpm_options;

	if ( ! empty( $args['options'] ) ) {
		foreach ( $args['options'] as $key => $option ):
			if ( isset( $rcpm_options[ $args['id'] ][ $key ] ) ) {
				$enabled = $option;
			} else {
				$enabled = null;
			}
			echo '<input name="rcpm_settings[' . $args['id'] . '][' . $key . ']" id="rcpm_settings_' . $args['id'] . '[' . $key . ']" type="checkbox" value="' . $option . '" ' . checked( $option, $enabled, false ) . '/>&nbsp;';
			echo '<label class="field-description" for="rcpm_settings_' . $args['id'] . '[' . $key . ']">' . $option . '</label><br/>';
		endforeach;
		echo '<p class="description">' . $args['desc'] . '</p>';
	}
}

/**
 * Radio Callback
 *
 * Renders radio boxes.
 *
 * @since 1.0.0
 *
 * @param array $args Arguments passed by the setting
 *
 * @global $rcpm_options Array of all the Recipe Manager Options
 * @return void
 */
function rcpm_radio_callback( $args ) {
	global $rcpm_options;

	foreach ( $args['options'] as $key => $option ) :
		$checked = false;

		if ( isset( $rcpm_options[ $args['id'] ] ) && $rcpm_options[ $args['id'] ] == $key ) {
			$checked = true;
		} elseif ( isset( $args['std'] ) && $args['std'] == $key && ! isset( $rcpm_options[ $args['id'] ] ) ) {
			$checked = true;
		}

		echo '<input name="rcpm_settings[' . $args['id'] . ']"" id="rcpm_settings_' . $args['id'] . '[' . $key . ']" type="radio" value="' . $key . '" ' . checked( true, $checked, false ) . '/>&nbsp;';
		echo '<label class="field-description" for="rcpm_settings_' . $args['id'] . '[' . $key . ']">' . $option . '</label><br/>';
	endforeach;

	echo '<p class="description">' . $args['desc'] . '</p>';
}

/**
 * Text Callback
 *
 * Renders text fields.
 *
 * @since 1.0.0
 *
 * @param array $args Arguments passed by the setting
 *
 * @global $rcpm_options Array of all the Recipe Manager Options
 * @return void
 */
function rcpm_text_callback( $args ) {
	global $rcpm_options;

	if ( isset( $rcpm_options[ $args['id'] ] ) ) {
		$value = $rcpm_options[ $args['id'] ];
	} else {
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}

	$readonly = $args['readonly'] === true ? ' readonly="readonly"' : '';
	$size     = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
	$html     = '<input type="text" class="' . $size . '-text" id="rcpm_settings_' . $args['id'] . '" name="rcpm_settings[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '"' . $readonly . '/>';
	$html .= '<label class="field-description" for="rcpm_settings_' . $args['id'] . '"> ' . $args['desc'] . '</label>';

	echo $html;
}

/**
 * Number Callback
 *
 * Renders number fields.
 *
 * @since 1.0.0
 *
 * @param array $args Arguments passed by the setting
 *
 * @global $rcpm_options Array of all the Recipe Manager Options
 * @return void
 */
function rcpm_number_callback( $args ) {
	global $rcpm_options;

	if ( isset( $rcpm_options[ $args['id'] ] ) ) {
		$value = $rcpm_options[ $args['id'] ];
	} else {
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}

	$max  = isset( $args['max'] ) ? $args['max'] : 999999;
	$min  = isset( $args['min'] ) ? $args['min'] : 0;
	$step = isset( $args['step'] ) ? $args['step'] : 1;

	$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
	$html = '<input type="number" step="' . esc_attr( $step ) . '" max="' . esc_attr( $max ) . '" min="' . esc_attr( $min ) . '" class="' . $size . '-text" id="rcpm_settings_' . $args['id'] . '" name="rcpm_settings[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
	$html .= '<label class="field-description" for="rcpm_settings_' . $args['id'] . '"> ' . $args['desc'] . '</label>';

	echo $html;
}

/**
 * Textarea Callback
 *
 * Renders textarea fields.
 *
 * @since 1.0.0
 *
 * @param array $args Arguments passed by the setting
 *
 * @global $rcpm_options Array of all the Recipe Manager Options
 * @return void
 */
function rcpm_textarea_callback( $args ) {
	global $rcpm_options;

	if ( isset( $rcpm_options[ $args['id'] ] ) ) {
		$value = $rcpm_options[ $args['id'] ];
	} else {
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}

	$html = '<textarea class="large-text" cols="50" rows="5" id="rcpm_settings_' . $args['id'] . '" name="rcpm_settings[' . $args['id'] . ']">' . esc_textarea( stripslashes( $value ) ) . '</textarea>';
	$html .= '<label class="field-description" for="rcpm_settings_' . $args['id'] . '"> ' . $args['desc'] . '</label>';

	echo $html;
}

/**
 * Password Callback
 *
 * Renders password fields.
 *
 * @since 1.0.0
 *
 * @param array $args Arguments passed by the setting
 *
 * @global $rcpm_options Array of all the Recipe Manager Options
 * @return void
 */
function rcpm_password_callback( $args ) {
	global $rcpm_options;

	if ( isset( $rcpm_options[ $args['id'] ] ) ) {
		$value = $rcpm_options[ $args['id'] ];
	} else {
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}

	$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
	$html = '<input type="password" class="' . $size . '-text" id="rcpm_settings_' . $args['id'] . '" name="rcpm_settings[' . $args['id'] . ']" value="' . esc_attr( $value ) . '"/>';
	$html .= '<label class="field-description" for="rcpm_settings_' . $args['id'] . '"> ' . $args['desc'] . '</label>';

	echo $html;
}

/**
 * Missing Callback
 *
 * If a function is missing for settings callbacks alert the user.
 *
 * @since 1.0.0
 *
 * @param array $args Arguments passed by the setting
 *
 * @return void
 */
function rcpm_missing_callback( $args ) {
	printf( __( 'The callback function used for the <strong>%s</strong> setting is missing.', 'recipe-manager' ), $args['id'] );
}

/**
 * Select Callback
 *
 * Renders select fields.
 *
 * @since 1.0.0
 *
 * @param array $args Arguments passed by the setting
 *
 * @global $rcpm_options Array of all the Recipe Manager Options
 * @return void
 */
function rcpm_select_callback( $args ) {
	global $rcpm_options;

	if ( isset( $rcpm_options[ $args['id'] ] ) ) {
		$value = $rcpm_options[ $args['id'] ];
	} else {
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}

	if ( isset( $args['placeholder'] ) ) {
		$placeholder = $args['placeholder'];
	} else {
		$placeholder = '';
	}

	if ( isset( $args['chosen'] ) ) {
		$chosen = 'class="rcpm-chosen"';
	} else {
		$chosen = '';
	}

	$html = '<select id="rcpm_settings_' . $args['id'] . '" name="rcpm_settings[' . $args['id'] . ']" ' . $chosen . 'data-placeholder="' . $placeholder . '" />';

	foreach ( $args['options'] as $option => $name ) :
		$selected = selected( $option, $value, false );
		$html .= '<option value="' . $option . '" ' . $selected . '>' . $name . '</option>';
	endforeach;

	$html .= '</select>';
	$html .= '<label class="field-description" for="rcpm_settings_' . $args['id'] . '"> ' . $args['desc'] . '</label>';

	echo $html;
}

/**
 * Dashicon Callback
 *
 * Renders select fields with dashicon preview.
 *
 * @since 1.0.0
 *
 * @param array $args Arguments passed by the setting
 *
 * @global $rcpm_options Array of all the Recipe Manager Options
 * @return void
 */
function rcpm_dashicon_callback( $args ) {
	global $rcpm_options;

	if ( isset( $rcpm_options[ $args['id'] ] ) ) {
		$value = $rcpm_options[ $args['id'] ];
	} else {
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}

	$readonly = $args['readonly'] === true ? ' readonly="readonly"' : '';
	$size     = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';

	$html = '<div class="dashicon-picker">';

	$html .= '<input class="regular-text" type="hidden" class="' . $size . '-text" id="rcpm_settings_' . $args['id'] . '" name="rcpm_settings[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '"' . $readonly . '/>';
	$html .= '<span id="rcpm_settings_' . $args['id'] . '_preview" class="dashicons-picker-preview dashicons ' . $value . '"></span>';

	$html .= '<input type="button" data-target="#rcpm_settings_' . $args['id'] . '" data-preview="#rcpm_settings_' . $args['id'] . '_preview" class="button dashicons-picker" value="' . __( 'Choose Icon', 'recipe-manager' ) . '" />';
	$html .= '<label class="field-description" for="rcpm_settings_' . $args['id'] . '"> ' . $args['desc'] . '</label>';

	$html .= '</div>';

	echo $html;
}

/**
 * Color select Callback
 *
 * Renders color select fields.
 *
 * @since 1.0.0
 *
 * @param array $args Arguments passed by the setting
 *
 * @global $rcpm_options Array of all the Recipe Manager Options
 * @return void
 */
function rcpm_color_select_callback( $args ) {
	global $rcpm_options;

	if ( isset( $rcpm_options[ $args['id'] ] ) ) {
		$value = $rcpm_options[ $args['id'] ];
	} else {
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}
	$html = '<select id="rcpm_settings_' . $args['id'] . '" name="rcpm_settings[' . $args['id'] . ']"/>';

	foreach ( $args['options'] as $option => $color ) :
		$selected = selected( $option, $value, false );
		$html .= '<option value="' . $option . '" ' . $selected . '>' . $color['label'] . '</option>';
	endforeach;

	$html .= '</select>';
	$html .= '<label class="field-description" for="rcpm_settings_' . $args['id'] . '"> ' . $args['desc'] . '</label>';

	echo $html;
}

/**
 * Rich Editor Callback
 *
 * Renders rich editor fields.
 *
 * @since 1.0.0
 *
 * @param array $args Arguments passed by the setting
 *
 * @global $rcpm_options Array of all the Recipe Manager Options
 * @global $wp_version WordPress Version
 */
function rcpm_rich_editor_callback( $args ) {
	global $rcpm_options, $wp_version;

	if ( isset( $rcpm_options[ $args['id'] ] ) ) {
		$value = $rcpm_options[ $args['id'] ];

		if ( empty( $args['allow_blank'] ) && empty( $value ) ) {
			$value = isset( $args['std'] ) ? $args['std'] : '';
		}
	} else {
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}

	$rows = isset( $args['size'] ) ? $args['size'] : 20;

	if ( $wp_version >= 3.3 && function_exists( 'wp_editor' ) ) {
		ob_start();
		wp_editor( stripslashes( $value ), 'rcpm_settings_' . $args['id'], array(
			'textarea_name' => 'rcpm_settings[' . $args['id'] . ']',
			'textarea_rows' => $rows,
		) );
		$html = ob_get_clean();
	} else {
		$html = '<textarea class="large-text" rows="10" id="rcpm_settings_' . $args['id'] . '" name="rcpm_settings[' . $args['id'] . ']">' . esc_textarea( stripslashes( $value ) ) . '</textarea>';
	}

	$html .= '<br/><label class="field-description" for="rcpm_settings_' . $args['id'] . '"> ' . $args['desc'] . '</label>';

	echo $html;
}

/**
 * Upload Callback
 *
 * Renders upload fields.
 *
 * @since 1.0.0
 *
 * @param array $args Arguments passed by the setting
 *
 * @global $rcpm_options Array of all the Recipe Manager Options
 * @return void
 */
function rcpm_upload_callback( $args ) {
	global $rcpm_options;

	if ( isset( $rcpm_options[ $args['id'] ] ) ) {
		$value = $rcpm_options[ $args['id'] ];
	} else {
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}

	$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
	$html = '<input type="text" class="' . $size . '-text" id="rcpm_settings_' . $args['id'] . '" name="rcpm_settings[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
	$html .= '<span>&nbsp;<input type="button" class="rcpm_settings_upload_button button-secondary" value="' . __( 'Upload File', 'recipe-manager' ) . '"/></span>';
	$html .= '<label class="field-description" for="rcpm_settings_' . $args['id'] . '"> ' . $args['desc'] . '</label>';

	echo $html;
}

/**
 * Color picker Callback
 *
 * Renders color picker fields.
 *
 * @since 1.0.0
 *
 * @param array $args Arguments passed by the setting
 *
 * @global $rcpm_options Array of all the Recipe Manager Options
 * @return void
 */
function rcpm_color_callback( $args ) {
	global $rcpm_options;

	if ( isset( $rcpm_options[ $args['id'] ] ) ) {
		$value = $rcpm_options[ $args['id'] ];
	} else {
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}

	$default = isset( $args['std'] ) ? $args['std'] : '';

	$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
	$html = '<input type="text" class="rcpm-color-picker" id="rcpm_settings_' . $args['id'] . '" name="rcpm_settings[' . $args['id'] . ']" value="' . esc_attr( $value ) . '" data-default-color="' . esc_attr( $default ) . '" />';
	$html .= '<label class="field-description" for="rcpm_settings_' . $args['id'] . '"> ' . $args['desc'] . '</label>';

	echo $html;
}

/**
 * Descriptive text callback.
 *
 * Renders descriptive text onto the settings field.
 *
 * @since 1.0.0
 *
 * @param array $args Arguments passed by the setting
 *
 * @return void
 */
function rcpm_descriptive_text_callback( $args ) {
	echo wp_kses_post( $args['desc'] );
}

/**
 * Registers the license field callback for Software Licensing
 *
 * @since 1.0.0
 *
 * @param array $args Arguments passed by the setting
 *
 * @global $rcpm_options Array of all the Recipe Manager Options
 * @return void
 */
if ( ! function_exists( 'rcpm_license_key_callback' ) ) {
	function rcpm_license_key_callback( $args ) {
		global $rcpm_options;

		if ( isset( $rcpm_options[ $args['id'] ] ) ) {
			$value = $rcpm_options[ $args['id'] ];
		} else {
			$value = isset( $args['std'] ) ? $args['std'] : '';
		}

		$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$html = '<input type="text" class="' . $size . '-text" id="rcpm_settings_' . $args['id'] . '" name="rcpm_settings[' . $args['id'] . ']" value="' . esc_attr( $value ) . '"/>';

		if ( 'valid' == get_option( $args['options']['is_valid_license_option'] ) ) {
			$html .= '<input type="submit" class="button-secondary" name="' . $args['id'] . '_deactivate" value="' . __( 'Deactivate License', 'recipe-manager' ) . '"/>';
		}
		$html .= '<label class="field-description" for="rcpm_settings_' . $args['id'] . '"> ' . $args['desc'] . '</label>';

		wp_nonce_field( $args['id'] . '-nonce', $args['id'] . '-nonce' );

		echo $html;
	}
}

/**
 * Hook Callback
 *
 * Adds a do_action() hook in place of the field
 *
 * @since 1.0.0
 *
 * @param array $args Arguments passed by the setting
 *
 * @return void
 */
function rcpm_hook_callback( $args ) {
	do_action( 'rcpm_' . $args['id'], $args );
}
