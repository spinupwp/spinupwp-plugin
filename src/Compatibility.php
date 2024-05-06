<?php

namespace SpinupWp;

use SpinupWp\Compatibility\ElementorPlugin;
use SpinupWp\Compatibility\CloudflarePlugin;

class Compatibility {
	/**
	 * @var Cache
	 */
	protected $cache;

	/**
	 * @var array
	 */
	protected $compatibilityClasses = array(
		ElementorPlugin::class,
        CloudflarePlugin::class,
	);

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
		foreach ( $this->compatibilityClasses as $compatibilityClass ) {
			( new $compatibilityClass( $this->cache ) )->init();
		}
	}
}