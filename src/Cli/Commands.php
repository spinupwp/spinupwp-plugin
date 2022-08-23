<?php

namespace SpinupWp\Cli;

use SpinupWp\Plugin;
use WP_CLI;

/**
 * Perform SpinupWP operations.
 *
 * ## EXAMPLES
 *
 *     # Show the status of SpinupWP
 *     $ wp spinupwp status
 */
class Commands {

	/**
	 * Update the Redis object cache drop-in.
	 *
	 * ## EXAMPLES
	 *
	 *     wp spinupwp update-object-cache-dropin
	 *
	 * @subcommand update-object-cache-dropin
	 */
	public function update_object_cache_dropin() {
		$result = Plugin::update_object_cache_dropin();

		if ( $result ) {
			WP_CLI::success( __( 'Object cache drop-in updated.', 'spinupwp' ) );
		} else {
			WP_CLI::error( __( 'Object cache drop-in could not be updated.', 'spinupwp' ) );
		}
	}

	/**
	 * Show the status of SpinupWP.
	 *
	 * ## EXAMPLES
	 *
	 *     wp spinupwp status
	 *
	 * @subcommand status
	 */
	public function status() {
		$status = WP_CLI::colorize( '%r' . __( 'Disabled', 'spinupwp' ) . '%n' );
		if ( defined( 'SPINUPWP_CACHE_PATH' ) || getenv( 'SPINUPWP_CACHE_PATH' ) ) {
			$status = WP_CLI::colorize( '%g' . __( 'Enabled', 'spinupwp' ) . '%n' );
		}

		WP_CLI::line( __( 'Page Cache: ', 'spinupwp' ) . $status );

		$status = WP_CLI::colorize( '%r' . __( 'Disabled', 'spinupwp' ) . '%n' );
		if ( wp_using_ext_object_cache() ) {
			$status = WP_CLI::colorize( '%g' . __( 'Enabled', 'spinupwp' ) . '%n' );
		}

		WP_CLI::line( __( 'Object Cache: ', 'spinupwp' ) . $status );
	}
}