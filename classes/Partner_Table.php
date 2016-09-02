<?php


namespace RCPM;

use RCPM;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Partner_Table' ) ) {
	require_once RCPM::$DIR . 'includes/libraries/class.partner-table.php';
}

class Partner_Table extends \Partner_Table {
	public $prefix = 'rcpm_';
}
