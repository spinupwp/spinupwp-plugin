<?php

namespace SpinupWp\Compatibility;

use SpinupWp\Cache;

class ElementorPlugin {
	/**
	 * @var Cache
	 */
	protected $cache;

	/**
	 * Compatibility constructor.
	 *
	 * @param Cache $cache
	 */
	public function __construct( Cache $cache ) {
		$this->cache = $cache;
	}

	/**
	 * Init
	 */
	public function init() {
		add_action( 'elementor/core/files/clear_cache', array( $this, 'purge_caches' ) );
	}

	/**
	 * Clear both the object and page caches.
	 */
	public function purge_caches() {
		if ( $this->cache->is_object_cache_enabled() ) {
			$this->cache->purge_object_cache();
		}

		if ( $this->cache->is_page_cache_enabled() ) {
			$this->cache->purge_page_cache();
		}
	}
}