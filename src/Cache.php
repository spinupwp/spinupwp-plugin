<?php

namespace DeliciousBrains\SpinupWp;

class Cache {
	/**
	 * @var Plugin
	 */
	private $plugin;

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
		if ( $this->is_object_cache_enabled() && $this->is_page_cache_enabled() ) {
			$this->plugin->add_admin_bar_item( 'Purge All Caches', 'purge-all' );
		}

		if ( $this->is_object_cache_enabled() ) {
			$this->plugin->add_admin_bar_item( 'Purge Object Cache', 'purge-object' );
		}

		if ( $this->is_page_cache_enabled() ) {
			$this->plugin->add_admin_bar_item( 'Purge Page Cache', 'purge-page' );
		}

		add_action( 'admin_init', array( $this, 'handle_manual_purge_action' ) );
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
		return true;
	}
}