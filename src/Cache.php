<?php

namespace DeliciousBrains\SpinupWp;

class Cache {
	/**
	 * @var Plugin
	 */
	private $plugin;

	/**
	 * @var string
	 */
	private $cache_path;

	/**
	 * Cache constructor.
	 *
	 * @param Plugin $plugin
	 */
	public function __construct( Plugin $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Init
	 */
	public function init() {
		$this->set_cache_path();

		if ( $this->is_object_cache_enabled() && $this->is_page_cache_enabled() ) {
			$this->plugin->add_admin_bar_item( 'Purge All Caches', 'purge-all' );
		}

		if ( $this->is_object_cache_enabled() ) {
			$this->plugin->add_admin_bar_item( 'Purge Object Cache', 'purge-object' );
		}

		if ( $this->is_page_cache_enabled() ) {
			$this->plugin->add_admin_bar_item( 'Purge Page Cache', 'purge-page' );
		}

		add_action( 'admin_init', [ $this, 'handle_manual_purge_action' ] );
		add_action( 'transition_post_status', [ $this, 'purge_post_on_status_transition' ], 10, 3 );
		add_action( 'delete_post', [ $this, 'purge_post_on_delete' ] );
	}


	/**
	 * Handle manual purge actions.
	 */
	public function handle_manual_purge_action() {
		$action = filter_input( INPUT_GET, 'spinupwp_action' );

		if ( ! $action || ! in_array( $action, array( 'purge-all', 'purge-object', 'purge-page' ) ) ) {
			return;
		}

		if ( ! wp_verify_nonce( filter_input( INPUT_GET, '_wpnonce' ), $action ) ) {
			return;
		}

		if ( 'purge-all' === $action ) {
			$purge = $this->purge_object_cache() && $this->purge_page_cache();
			$type  = 'all';
		}

		if ( 'purge-object' === $action ) {
			$purge = $this->purge_object_cache();
			$type  = 'object';
		}

		if ( 'purge-page' === $action ) {
			$purge = $this->purge_page_cache();
			$type  = 'page';
		}

		$redirect_url = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : admin_url();

		wp_safe_redirect( add_query_arg( array(
			'purge_success' => (int) $purge,
			'cache_type'    => $type,
		), $redirect_url ) );
	}

	/**
	 * Transition post status.
	 *
	 * When a post is transitioned to 'publish' for the first time purge the
	 * entire site cache. This ensures blog pages, category archives, author archives
	 * and search results are accurate. Otherwise, only update the current post URL.
	 *
	 * @param string $new_status
	 * @param string $old_status
	 * @param WP_Post $post
	 *
	 * @return bool
	 */
	public function purge_post_on_status_transition( $new_status, $old_status, $post ) {
		if ( ! $this->cache_path ) {
			return false;
		}

		if ( ! in_array( get_post_type( $post ), array( 'post', 'page' ) ) ) {
			return false;
		}

		if ( $new_status !== 'publish' ) {
			return false;
		}

		if ( $old_status === 'publish' ) {
			return $this->purge_post( $post );
		}

		return $this->purge_cache();
	}

	/**
	 * Ensure the site cache is purged when a post is deleted via wp_delete_post().
	 * This is especially needed if a site doesn't use the trash, so we won't catch on post status change.
	 *
	 * @param int $post_id
	 */
	public function purge_post_on_delete( $post_id ) {
		$default_post_types = $this->get_post_types_needing_single_purge();
		$post_types         = array_merge( $default_post_types, $this->get_post_types_needing_site_purge() );
		$post_type          = get_post_type( $post_id );

		if ( in_array( $post_type, $post_types ) ) {
			$this->purge_page_cache();
		}
	}

	/**
	 * Set the base cache path.
	 */
	private function set_cache_path() {
		if ( getenv( 'SPINUPWP_CACHE_PATH' ) ) {
			$this->cache_path = getenv( 'SPINUPWP_CACHE_PATH' );
		}

		if ( defined( 'SPINUPWP_CACHE_PATH' ) ) {
			$this->cache_path = SPINUPWP_CACHE_PATH;
		}
	}

	/**
	 * Is the object cache enabled?
	 *
	 * @return bool
	 */
	private function is_object_cache_enabled() {
		return wp_using_ext_object_cache();
	}

	/**
	 * Is the page cache enabled?
	 *
	 * @return bool
	 */
	private function is_page_cache_enabled() {
		return defined( 'SPINUPWP_CACHE_PATH' ) || getenv( 'SPINUPWP_CACHE_PATH' );
	}

	/**
	 * Purge entire object cache.
	 *
	 * @return bool
	 */
	private function purge_object_cache() {
		return wp_cache_flush();
	}

	/**
	 * Purge entire cache.
	 *
	 * @return bool
	 */
	private function purge_page_cache() {
		return $this->delete( $this->cache_path, true );
	}

	/**
	 * Purge the current post URL.
	 *
	 * @param WP_Post $post
	 *
	 * @return bool
	 */
	private function purge_post( $post ) {
		return $this->purge_url( get_permalink( $post ) );
	}

	/**
	 * Purge a single URL from the cache.
	 *
	 * @param string $url
	 *
	 * @return bool
	 */
	private function purge_url( $url ) {
		$path = $this->get_cache_path_for_url( $url );

		return $this->delete( $path );
	}

	/**
	 * Get's the cache file path for a given URL.
	 *
	 * Must be using the default Nginx cache options (levels=1:2)
	 * and (fastcgi_cache_key "$scheme$request_method$host$request_uri").
	 * https://www.digitalocean.com/community/tutorials/how-to-setup-fastcgi-caching-with-nginx-on-your-vps#purging-the-cache
	 *
	 * @param string $url
	 *
	 * @return string
	 */
	private function get_cache_path_for_url( $url ) {
		$parsed_url = parse_url( trailingslashit( $url ) );
		$cache_key  = md5( $parsed_url['scheme'] . 'GET' . $parsed_url['host'] . $parsed_url['path'] );
		$cache_path = substr( $cache_key, - 1 ) . '/' . substr( $cache_key, - 3, 2 ) . '/' . $cache_key;

		return trailingslashit( $this->cache_path ) . $cache_path;
	}

	/**
	 * Delete a file/dir from the local filesystem.
	 *
	 * @param string $path Absolute path to file
	 * @param bool $recursive
	 *
	 * @return bool
	 */
	private function delete( $path, $recursive = false ) {
		error_log( $path );
		if ( file_exists( $path ) && is_writable( $path ) ) {
			require_once( ABSPATH . '/wp-admin/includes/file.php' );

			$context = $path;
			if ( is_file( $path ) ) {
				$context = dirname( $path );
			}

			if ( ! WP_Filesystem( false, $context, true ) ) {
				return false;
			}

			global $wp_filesystem;
			$wp_filesystem->delete( $path, $recursive );

			return true;
		}

		return $this->delete_via_cache_daemon( $path );
	}

	/**
	 * Use SpinupWP daemon to purge cache.
	 *
	 * @param string $path
	 *
	 * @return bool
	 */
	private function delete_via_cache_daemon( $path ) {
		$fp = @fsockopen( '127.0.0.1', '7836' );

		if ( $fp ) {
			fwrite( $fp, $path . "\n" );

			$result = fread( $fp, 512 );
			fclose( $fp );

			return (bool) preg_match( '/^Purge:\sSuccess/', $result );
		}

		return false;
	}


	/**
	 * These post types have single.php pages so we can purge individual posts in the cache on edit.
	 *
	 * @return array
	 */
	protected function get_post_types_needing_single_purge() {
		return apply_filters( 'spinupwp_purge_single_post_types', [ 'post', 'page' ] );
	}

	/**
	 * These post types can have data rendered anywhere in the site so whole cache purges are needed.
	 *
	 * @return array
	 */
	protected function get_post_types_needing_site_purge() {
		return apply_filters( 'spinupwp_purge_site_post_types', [] );
	}
}