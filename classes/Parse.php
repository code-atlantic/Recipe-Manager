<?php


namespace RCPM;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class Parse {
	/**
	 * Parse a textarea value for individual rows, line by line.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $input The input to parse.
	 * @param  string $type The type of items to parse. 'ingredient' or 'step'.
	 *
	 * @return array  $items The parsed items.
	 */
	public static function rows( $input, $type ) {

		// Split the input into individual lines and trim any whitespace.
		$lines = preg_split( '/\r\n|[\r\n]/', $input );
		$lines = array_map( 'trim', $lines );

		$items = false;

		if ( ! empty( $lines ) ) {

			$items = array();

			foreach ( $lines as $line ) {

				// Discard blank lines.
				if ( '' == $line ) {
					continue;
				}

				if ( 'ingredients' == $type ) {

					$measure     = '';
					$unit        = '';
					$label       = '';
					$note        = '';
					$description = $line;

					$parsed_measure = self::measure( $line );

					if ( $parsed_measure ) {

						$measure = $parsed_measure['result'];

						// Remove the measure and format the description.
						$description = substr( $description, $parsed_measure['end'] );
						$description = trim( $description );

						$parsed_unit = self::unit( $description );

						if ( $parsed_unit ) {

							$description = substr( $description, $parsed_unit['end'] );

							$unit = $parsed_unit['result'];
						}
					}

					$description = trim( $description );

					$parsed_note = self::note( $description );

					if ( $parsed_note ) {
						$description = substr( $description, 0, $parsed_note['length'] * - 1 );
						$note        = $parsed_note['result'];
					}


					$items[] = array(
						'measure' => $measure,
						'unit'    => $unit,
						'label'   => ucwords( $description ),
						'note'    => ucwords( $note ),
					);

				} else {
					$description = trim( $line );
					$note        = '';

					/*
					$parsed_note = self::parse_note( $description, true );

					if ( $parsed_note ) {
						$description = substr( $description, 0, $parsed_note['length'] * -1 );
						$note = $parsed_note['result'];
					}
					*/

					$items[] = array(
						'description' => $description,
						'note'        => $note,
					);
				}
			}
		}

		return $items;
	}

	/**
	 * Parse out measure values from the start of a given string.
	 *
	 * @since  1.0.0
	 * @access private
	 *
	 * @param  string $string The string from user input.
	 *
	 * @return array|false $result The resulting measure information or false if none exists.
	 */
	private static function measure( $string ) {

		// Isolate the first word.
		$first_word = strtok( $string, ' ' );

		// Get measure string if it meets our criteria.
		$measure_length = strspn( $first_word, '0123456789/.' );
		$measure_string = substr( $string, 0, $measure_length );

		if ( $measure_string ) {

			// Format the measure.
			$measure = trim( $measure_string );

			// Isolate the second word to check for fractions or floats.
			$string      = substr( $string, $measure_length );
			$string      = trim( $string );
			$second_word = strtok( $string, ' ' );

			// Filter the second word against allowed characters.
			$fraction_length = strspn( $second_word, '0123456789/.' );
			$fraction_string = substr( $string, 0, $fraction_length );

			// If a fraction or float does indeed exist, parse it.
			if ( $fraction_string ) {

				// Update the final measure string to include the fraction or float.
				$measure_string = $measure_string . ' ' . $fraction_string;
				$measure_length = strlen( $measure_string );

				// Format the fraction to a float.
				$fraction_measure = trim( $fraction_string );

				// Add the two together for one final value.
				$measure = $measure . ' ' . $fraction_measure;
			}

			$result = array(
				'start'  => 0,
				'end'    => $measure_length,
				'result' => $measure,
			);

		} else {

			$result = false;

		}

		return $result;
	}

	/**
	 * Parse out a unit value from the start of a given string.
	 *
	 * @since  1.0.0
	 * @access private
	 *
	 * @param  string $string The string from user input.
	 *
	 * @return array|false $result The resulting unit information or no match was found.
	 */
	private static function unit( $string ) {

		$_unit = '';
		$end   = 0;

		// Isolate the first word.
		$first_word = strtok( $string, ' ' );

		// Get the available measurement units.
		$units  = rcpm_measurement_units();
		$_units = array();

		// Convert to flat array of key => lowercase labels.
		foreach ( $units as $unit => $labels ) {
			foreach ( $labels as $label ) {
				$_units[ strtolower( $label ) ] = $unit;
			}
		}

		// Loop through each unit & its labels for a match.
		foreach ( $_units as $label => $unit ) {

			// Check that no match has been found yet.
			if ( '' != $_unit && 0 == $end ) {
				continue;
			}

			// Force lowercase and remove . from input unit.
			$_first_word = strtolower( $first_word );
			$_first_word = str_replace( '.', '', $_first_word );

			// If we have a match, set the values.
			if ( $_first_word === $label ) {

				$_unit = $unit;
				$end   = strlen( $first_word );

				break;
			}
		}

		if ( $_unit && $end ) {

			$result = array(
				'result' => $_unit,
				'start'  => 0,
				'end'    => $end,
			);

		} else {
			$result = false;
		}

		return $result;
	}

	/**
	 * Parse out a note from a given string.
	 *
	 * @since  1.0.0
	 * @access private
	 *
	 * @param  string $description The string from user input.
	 * @param  bool $skip_commas Used to skip the check for comma notes.
	 *
	 * @return array|false $result The resulting unit information or no match was found.
	 */
	private static function note( $description, $skip_commas = false ) {
		$note   = false;
		$length = false;
		if ( ! $skip_commas && strpos( $description, ',' ) ) {
			$split = explode( ',', $description );

			if ( ! empty( $split[1] ) && $split[1] != '' ) {
				$note   = $split[1];
				$length = strlen( $split[1] ) + 1;
			}
		} elseif ( strpos( $description, '(' ) && strpos( $description, ')' ) ) {
			$split = explode( '(', $description );

			if ( ! empty( $split[1] ) && $split[1] != '' ) {
				$note   = trim( $split[1], ' ()' );
				$length = strlen( $split[1] ) + 1;
			}
		}

		if ( $note && $length ) {

			$result = array(
				'result' => $note,
				'length' => $length,
			);

		} else {
			$result = false;
		}

		return $result;
	}

}
