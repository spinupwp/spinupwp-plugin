<?php

namespace DeliciousBrains\SpinupWp;

class Plugin {

	/**
	 * @var string
	 */
	public $path;

	/**
	 * @var Cache
	 */
	public $cache;

	public function __construct( $path )
	{
		$this->path = $path;
	}

	/**
	 * Run the SpinupWP plugin.
	 */
	public function run() {
		$admin_bar   = new AdminBar;
		$this->cache = new Cache( $admin_bar, new Cli );

		$this->cache->init();
		$admin_bar->init();

		register_activation_hook( $this->path, array( Plugin::class, 'install' ) );
	}

	/**
	 * Perform actions on plugin activation.
	 */
	public static function install() {
		error_log( 'Test' );
	}
}