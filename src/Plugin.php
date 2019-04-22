<?php

namespace DeliciousBrains\SpinupWp;

class Plugin {

	/**
	 * @var string
	 */
	public $path;

	/**
	 * @var string
	 */
	public $url;

	/**
	 * @var Cache
	 */
	public $cache;

	public function __construct( $path ) {
		$this->path = $path;
		$this->url  = plugin_dir_url( $path );
	}

	/**
	 * Run the SpinupWP plugin.
	 */
	public function run() {
		if ( getenv( 'SPINUPWP_SITE' ) ) {
			$admin_bar     = new AdminBar;
			$admin_notices = new AdminNotices( $this->url );
			$this->cache   = new Cache( $admin_bar, new Cli );

			$this->cache->init();
			$admin_bar->init();
			$admin_notices->init();
		}

		register_activation_hook( $this->path, array( Plugin::class, 'install' ) );
		register_uninstall_hook( $this->path, array( Plugin::class, 'uninstall' ) );
	}

	/**
	 * Perform actions on plugin activation.
	 */
	public static function install() {
		$plugin_path = untrailingslashit( dirname( __DIR__ ) );
		$wpmu_dir    = untrailingslashit( WPMU_PLUGIN_DIR );

		if ( ! file_exists( $wpmu_dir . '/spinupwp-debug-log-path.php' ) ) {
			@copy( $plugin_path . '/mu-plugins/spinupwp-debug-log-path.php', $wpmu_dir . '/spinupwp-debug-log-path.php' );
		}
		
	}

	/**
	 * Perform actions on plugin uninstall.
	 */
	public static function uninstall() {
		$wpmu_dir = untrailingslashit( WPMU_PLUGIN_DIR );

		if ( file_exists( $wpmu_dir . '/spinupwp-debug-log-path.php' ) ) {
			@unlink( $wpmu_dir . '/spinupwp-debug-log-path.php' );
		}
	}
}