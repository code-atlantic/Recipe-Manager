<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function rcpm_available_addons() {
	$json_data = file_get_contents( RCPM::$DIR . 'includes/addon-list.json' );
	return json_decode( $json_data, true );
}

add_filter( 'rcpm_existing_addon_images', 'rcpm_core_addon_images', 10 );
function rcpm_core_addon_images( $array ) {
	return array_merge( $array, array() );
}
