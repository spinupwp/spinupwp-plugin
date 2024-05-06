<?php

namespace SpinupWp\Compatibility;

use SpinupWp\Cache;

class CloudflarePlugin {
    /**
     * @var Cache
     */
    protected $cache;

    /**
     * @var Cache
     */
    protected $cloudflareHooks;

	/**
	 * Compatibility constructor.
	 *
	 */
	public function __construct( Cache $cache ) {
        $this->cache = $cache;
	}
    
    public function init() {
        if ( class_exists( 'CF\WordPress\Hooks' ) ) {
            $this->cloudflareHooks = new \CF\WordPress\Hooks();
            add_filter( 'spinupwp_site_purged', array( $this, 'clear_cloudflare_cache' ) );
        }
    }

    /**
     * Purge the cache when the site is purged in SpinupWP.
     */
    public function clear_cloudflare_cache( ) {
        // log debug message
        error_log( 'Purging Cloudflare cache from SpinupWP' );
        $this->cloudflareHooks->purgeCacheEverything();
    }
}