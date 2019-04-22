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
		register_uninstall_hook( $this->path, array( Plugin::class, 'uninstall' ) );
	}

	/**
	 * Perform actions on plugin activation.
	 */
	public static function install() {
		global $wp_filesystem;

		$plugin_path = untrailingslashit( dirname( __DIR__ ) );

		if ( ! file_exists( WPMU_PLUGIN_DIR . '/spinupwp-debug-log-path.php' ) ) {
			$wp_filesystem->copy( $plugin_path . '/mu-plugins/spinupwp-debug-log-path.php', WPMU_PLUGIN_DIR . '/spinupwp-debug-log-path.php', true );
		}
		
	}

	/**
	 * Perform actions on plugin uninstall.
	 */
	public static function uninstall() {
		global $wp_filesystem;

		if ( file_exists( WPMU_PLUGIN_DIR . '/spinupwp-debug-log-path.php' ) ) {
			$wp_filesystem->delete( WPMU_PLUGIN_DIR . '/spinupwp-debug-log-path.php' );
		}
	}
}