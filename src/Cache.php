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
		if ( wp_using_ext_object_cache() ) {
			$this->plugin->add_admin_bar_item( 'Purge Object Cache', 'purge-object' );
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

		if ( wp_using_ext_object_cache() && 'purge-object' === $action ) {
			$purge = wp_cache_flush();
			$type  = 'object';
		}

		$redirect_url = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : admin_url();

		wp_safe_redirect( add_query_arg( array(
			'purge_success' => (int) $purge,
			'cache_type'    => $type,
		), $redirect_url ) );
	}
}