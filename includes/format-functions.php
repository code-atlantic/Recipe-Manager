<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


function rcpm_hhmm_to_seconds( $hhmm = '00:00' ) {
	$time = explode( ':', $hhmm );
	return ($time[0] * 3600) + ($time[1] * 60);
}

function rcpm_hhmm_to_hm( $hhmm = '00:00' ) {
	$time = explode( ':', $hhmm );

	$hours = $time[0];
	$minutes = $time[1];

	$format = $hours > 0 ? __( '%1$dh %2$dm', 'recipe-manager' ) : __( '%2$dm', 'recipe-manager' );

	return sprintf( $format, $hours, $minutes );
}


function rcpm_hhmm_to_hrmin( $hhmm = '00:00' ) {
	$time = explode( ':', $hhmm );

	$hours = $time[0];
	$minutes = $time[1];

	$format = $hours > 0 ? __( '%1$d hr %2$d min', 'recipe-manager' ) : __( '%2$d min', 'recipe-manager' );

	return sprintf( $format, $hours, $minutes );
}

function rcpm_seconds_to_hhmm( $seconds = 0 ) {
	$minutes = intval( $seconds / 60 ) % 60;
	$hours = intval( $seconds / 3600 ) % 24;
	return sprintf( "%02d", $hours ) . ':' . sprintf( "%02d", $minutes );
}


function rcpm_seconds_to_schema_duration( $seconds = 0 ) {
	$minutes = intval( $seconds / 60 ) % 60;
	$hours = intval( $seconds / 3600 ) % 24;

	$duration = 'PT';

	if ( $hours > 0 ) {
		$duration .= $hours . 'H';
	}
	if ( $minutes > 0 ) {
		$duration .= $minutes . 'M';
	}

	return $duration;
}

function rcpm_hhmm_to_schema_duration( $hhmm = '00:00' ) {
	return rcpm_seconds_to_schema_duration( rcpm_hhmm_to_seconds( $hhmm ) );
}


function rcpm_measurement_units( $type = null ) {
	$units = apply_filters( 'rcpm_measurement_units', array(
		'cup' => array(
			'singular' => __( 'Cup', 'recipe-manager' ),
			'plural' => __( 'Cups', 'recipe-manager' ),
			'abbreviation' => __( 'cup', 'recipe-manager' ),
			'abbreviations' => __( 'cups', 'recipe-manager' ),
			'shorthand' => __( 'c', 'recipe-manager' ),
		),
		'tbsp' => array(
			'singular' => __( 'Tablespoon', 'recipe-manager' ),
			'plural' => __( 'Tablespoons', 'recipe-manager' ),
			'abbreviation' => __( 'tbsp', 'recipe-manager' ),
			'abbreviations' => __( 'tbsps', 'recipe-manager' ),
			'shorthand' => __( 'T', 'recipe-manager' ),
		),
		'tsp' => array(
			'singular' => __( 'Teaspoon', 'recipe-manager' ),
			'plural' => __( 'Teaspoons', 'recipe-manager' ),
			'abbreviation' => __( 'tsp', 'recipe-manager' ),
			'abbreviations' => __( 'tsps', 'recipe-manager' ),
			'shorthand' => __( 't', 'recipe-manager' ),
		),
		'floz' => array(
			'singular' => __( 'Fluid Ounce', 'recipe-manager' ),
			'plural' => __( 'Fluid Ounces', 'recipe-manager' ),
			'abbreviation' => __( 'fl oz', 'recipe-manager' ),
			'abbreviations' => __( 'fl ozs', 'recipe-manager' ),
			'shorthand' => __( 'fl oz', 'recipe-manager' ),
		),
		'gill' => array(
			'singular' => __( 'Gill', 'recipe-manager' ),
			'plural' => __( 'Gills', 'recipe-manager' ),
			'abbreviation' => __( 'gill', 'recipe-manager' ),
			'abbreviations' => __( 'gills', 'recipe-manager' ),
			'shorthand' => __( 'gill', 'recipe-manager' ),
		),
		'pint' => array(
			'singular' => __( 'Pint', 'recipe-manager' ),
			'plural' => __( 'Pints', 'recipe-manager' ),
			'abbreviation' => __( 'pt', 'recipe-manager' ),
			'abbreviations' => __( 'pts', 'recipe-manager' ),
			'shorthand' => __( 'p', 'recipe-manager' ),
		),
		'quart' => array(
			'singular' => __( 'Quart', 'recipe-manager' ),
			'plural' => __( 'Quarts', 'recipe-manager' ),
			'abbreviation' => __( 'qt', 'coqtoked' ),
			'abbreviations' => __( 'qts', 'recipe-manager' ),
			'shorthand' => __( 'q', 'recipe-manager' ),
		),
		'gallon' => array(
			'singular' => __( 'Gallon', 'recipe-manager' ),
			'plural' => __( 'Gallons', 'recipe-manager' ),
			'abbreviation' => __( 'gal', 'recipe-manager' ),
			'abbreviations' => __( 'gals', 'recipe-manager' ),
			'shorthand' => __( 'g', 'recipe-manager' ),
		),
		'milliliter' => array(
			'singular' => __( 'Milliliter', 'recipe-manager' ),
			'plural' => __( 'Milliliters', 'recipe-manager' ),
			'abbreviation' => __( 'mL', 'recipe-manager' ),
			'abbreviations' => __( 'mLs', 'recipe-manager' ),
			'shorthand' => __( 'mL', 'recipe-manager' ),
		),
		'liter' => array(
			'singular' => __( 'Liter', 'recipe-manager' ),
			'plural' => __( 'Liters', 'recipe-manager' ),
			'abbreviation' => __( 'L', 'recipe-manager' ),
			'abbreviations' => __( 'Ls', 'recipe-manager' ),
			'shorthand' => __( 'L', 'recipe-manager' ),
		),
		'deciliter' => array(
			'singular' => __( 'Deciliter', 'recipe-manager' ),
			'plural' => __( 'Deciliters', 'recipe-manager' ),
			'abbreviation' => __( 'dL', 'recipe-manager' ),
			'abbreviations' => __( 'dLs', 'recipe-manager' ),
			'shorthand' => __( 'dL', 'recipe-manager' ),
		),
		'pound' => array(
			'singular' => __( 'Pound', 'recipe-manager' ),
			'plural' => __( 'Pounds', 'recipe-manager' ),
			'abbreviation' => __( 'lb', 'recipe-manager' ),
			'abbreviations' => __( 'lbs', 'recipe-manager' ),
			'shorthand' => __( '#', 'recipe-manager' ),
		),
		'ounce' => array(
			'singular' => __( 'Ounce', 'recipe-manager' ),
			'plural' => __( 'Ounces', 'recipe-manager' ),
			'abbreviation' => __( 'oz', 'recipe-manager' ),
			'abbreviations' => __( 'ozs', 'recipe-manager' ),
			'shorthand' => __( 'oz', 'recipe-manager' ),
		),
		'milligram' => array(
			'singular' => __( 'Milligram', 'recipe-manager' ),
			'plural' => __( 'Milligrams', 'recipe-manager' ),
			'abbreviation' => __( 'mg', 'recipe-manager' ),
			'abbreviations' => __( 'mgs', 'recipe-manager' ),
			'shorthand' => __( 'mg', 'recipe-manager' ),
		),
		'kilogram' => array(
			'singular' => __( 'Kilogram', 'recipe-manager' ),
			'plural' => __( 'Kilograms', 'recipe-manager' ),
			'abbreviation' => __( 'kg', 'recipe-manager' ),
			'abbreviations' => __( 'kgs', 'recipe-manager' ),
			'shorthand' => __( 'kg', 'recipe-manager' ),
		),

		'millimeter' => array(
			'singular' => __( 'Millimeter', 'recipe-manager' ),
			'plural' => __( 'Millimeters', 'recipe-manager' ),
			'abbreviation' => __( 'mm', 'recipe-manager' ),
			'abbreviations' => __( 'mm', 'recipe-manager' ),
			'shorthand' => __( 'mm', 'recipe-manager' ),
		),
		'centimeter' => array(
			'singular' => __( 'Centimeter', 'recipe-manager' ),
			'plural' => __( 'Centimeters', 'recipe-manager' ),
			'abbreviation' => __( 'cm', 'recipe-manager' ),
			'abbreviations' => __( 'cm', 'recipe-manager' ),
			'shorthand' => __( 'cm', 'recipe-manager' ),
		),
		'meter' => array(
			'singular' => __( 'Meter', 'recipe-manager' ),
			'plural' => __( 'Meters', 'recipe-manager' ),
			'abbreviation' => __( 'm', 'recipe-manager' ),
			'abbreviations' => __( 'm', 'recipe-manager' ),
			'shorthand' => __( 'm', 'recipe-manager' ),
		),
		'inch' => array(
			'singular' => __( 'Inch', 'recipe-manager' ),
			'plural' => __( 'Inches', 'recipe-manager' ),
			'abbreviation' => __( 'in', 'recipe-manager' ),
			'abbreviations' => __( 'in', 'recipe-manager' ),
			'shorthand' => __( '"', 'recipe-manager' ),
		),
	) );

	if ( ! $type ) {
		return $units;
	}

	$return = array();
	foreach ( $units as $key => $types ) {
		if ( ! isset( $types[ $type ] ) ) {
			continue;
		}
		$return[ $key ] = $types[ $type ];
	}

	return $return;

}

function rcpm_get_measurement_unit( $key, $type = null ) {
	$measurement_units = rcpm_measurement_units( $type );

	if ( ! isset( $measurement_units[ $key ] ) ) {
		return;
	}

	return $measurement_units[ $key ];
}