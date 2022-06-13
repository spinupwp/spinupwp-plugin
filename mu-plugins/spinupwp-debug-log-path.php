<?php
/*
Plugin Name: SpinupWP Debug Log Path
Plugin URI: https://spinupwp.com
Description: Set debug.log location for SpinupWP.
Author: SpinupWP
Version: 1.0
Author URI: https://spinupwp.com/
*/

if ( getenv( 'SPINUPWP_LOG_PATH' ) && WP_DEBUG && WP_DEBUG_LOG ) {
	ini_set( 'error_log', getenv( 'SPINUPWP_LOG_PATH' ) );
}