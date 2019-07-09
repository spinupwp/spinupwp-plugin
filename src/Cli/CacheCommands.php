<?php

namespace DeliciousBrains\SpinupWp\Cli;

use WP_CLI;

/**
 * Perform SpinupWP operations.
 *
 * ## EXAMPLES
 *
 *     # Purge the entire SpinupWP page cache
 *     $ wp spinupwp purge-site
 */
class CacheCommands {

	/**
	 * Purge the entire SpinupWP page cache.
	 *
	 * ## EXAMPLES
	 *
	 *     wp spinupwp purge-site
	 *
	 * @subcommand purge-site
	 */
	public function purge_site() {
		if ( spinupwp()->cache->purge_page_cache() ) {
			WP_CLI::success( __( 'Site cache purged.', 'spinupwp' ) );
		} else {
			WP_CLI::error( __( 'Site cache could not be purged.', 'spinupwp' ) );
		}
	}

	/**
	 * Purge a single post from the SpinupWP page cache.
	 *
	 * ## OPTIONS
	 *
	 * <post_id>
	 * : The ID of the post to purge.
	 *
	 * ## EXAMPLES
	 *
	 *     wp spinupwp purge-post 123
	 *
	 * @subcommand purge-post
	 */
	public function purge_post( $args ) {
		$post = get_post( $args[0] );

		if ( ! $post ) {
			WP_CLI::error( __( 'Post not found.', 'spinupwp' ) );

			return;
		}

		if ( spinupwp()->cache->purge_post( $post ) ) {
			WP_CLI::success( __( 'Post purged from cache.', 'spinupwp' ) );
		} else {
			WP_CLI::error( __( 'Post could not be purged from cache.', 'spinupwp' ) );
		}
	}

	/**
	 * Purge a single URL from the SpinupWP page cache.
	 *
	 * ## OPTIONS
	 *
	 * <url>
	 * : The URL to purge.
	 *
	 * ## EXAMPLES
	 *
	 *     wp spinupwp purge-url https://example.com
	 *
	 * @subcommand purge-url
	 */
	public function purge_url( $args ) {
		if ( spinupwp()->cache->purge_url( $args[0] ) ) {
			WP_CLI::success( __( 'URL purged from cache.', 'spinupwp' ) );
		} else {
			WP_CLI::error( __( 'URL could not be purged from cache.', 'spinupwp' ) );
		}
	}

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
		$wpcontent_dir = untrailingslashit( WP_CONTENT_DIR );

		if ( file_exists( $wpcontent_dir . '/object-cache.php' ) ) {
			@unlink( $wpcontent_dir . '/object-cache.php' );
		}

		$plugin_path = untrailingslashit( dirname( dirname( __DIR__ ) ) );

		if ( @copy( $plugin_path . '/drop-ins/object-cache.php', $wpcontent_dir . '/object-cache.php' ) ) {
			WP_CLI::success( __( 'Object cache drop-in updated.', 'spinupwp' ) );
		} else {
			WP_CLI::error( __( 'Object cache drop-in could not be updated.', 'spinupwp' ) );
		}

		if ( function_exists( 'wp_cache_flush' ) ) {
			wp_cache_flush();
		}
	}

	/**
	 * Show the status of SpinupWP caches.
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