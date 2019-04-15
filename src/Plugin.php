<?php

namespace DeliciousBrains\SpinupWp;

class Plugin {

	/**
	 * @var Cache
	 */
	public $cache;

	/**
	 * Run the SpinupWP plugin.
	 */
	public function run() {
		$admin_bar   = new AdminBar;
		$this->cache = new Cache( $admin_bar, new Cli );

		$this->cache->init();
		$admin_bar->init();
	}
}