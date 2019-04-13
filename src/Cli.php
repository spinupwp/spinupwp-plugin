<?php

namespace DeliciousBrains\SpinupWp;

use WP_CLI;

class Cli {
	public function register_command( $callable ) {
		if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
			return false;
		}

		return WP_CLI::add_command( 'spinupwp', $callable );	
	}
}