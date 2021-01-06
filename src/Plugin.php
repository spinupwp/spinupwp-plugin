<?php

namespace DeliciousBrains\SpinupWp;

use DeliciousBrains\SpinupWp\Cli\Commands;

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
		$admin_bar     = new AdminBar( $this->url );
		$admin_notices = new AdminNotices( $this->url );
		$cli           = new Cli;

		$cli->register_command( 'spinupwp', Commands::class );
		$this->cache = new Cache( $admin_bar, $cli );

		$compatibility = new Compatibility( $this->cache );

		$this->cache->init();
		$admin_bar->init();
		$admin_notices->init();
		$compatibility->init();

		if ( getenv( 'SPINUPWP_SITE' ) ) {
			register_activation_hook( $this->path, array( Plugin::class, 'install' ) );
			register_uninstall_hook( $this->path, array( Plugin::class, 'uninstall' ) );
			add_action( 'admin_init', array( $this, 'admin_init' ) );

			$site_health = new SiteHealth();
			$site_health->init();
		}
	}

	/**
	 * Perform actions on plugin activation.
	 */
	public static function install() {
		$plugin_path   = untrailingslashit( dirname( __DIR__ ) );
		$wpmu_dir      = untrailingslashit( WPMU_PLUGIN_DIR );
		$wpcontent_dir = untrailingslashit( WP_CONTENT_DIR );

		if ( ! file_exists( $wpmu_dir . '/spinupwp-debug-log-path.php' ) ) {
			wp_mkdir_p( $wpmu_dir );
			@copy( $plugin_path . '/mu-plugins/spinupwp-debug-log-path.php', $wpmu_dir . '/spinupwp-debug-log-path.php' );
		}

		if ( file_exists( $wpcontent_dir . '/object-cache.php' ) ) {
			@unlink( $wpcontent_dir . '/object-cache.php' );
		}

		@copy( $plugin_path . '/drop-ins/object-cache.php', $wpcontent_dir . '/object-cache.php' );

		if ( function_exists( 'wp_cache_flush' ) ) {
			wp_cache_flush();
		}
	}

	/**
	 * Perform actions on plugin uninstall.
	 */
	public static function uninstall() {
		$wpmu_dir      = untrailingslashit( WPMU_PLUGIN_DIR );
		$wpcontent_dir = untrailingslashit( WP_CONTENT_DIR );

		if ( file_exists( $wpmu_dir . '/spinupwp-debug-log-path.php' ) ) {
			@unlink( $wpmu_dir . '/spinupwp-debug-log-path.php' );
		}

		if ( function_exists( 'wp_cache_flush' ) ) {
			wp_cache_flush();
		}

		if ( file_exists( $wpcontent_dir . '/object-cache.php' ) ) {
			@unlink( $wpcontent_dir . '/object-cache.php' );
		}

		delete_site_option( 'spinupwp_redis_cache_disabled' );
		delete_site_option( 'spinupwp_mail_notice_dismissed' );
		delete_site_option( 'spinupwp_redis_cache_disabled_notice_dismissed' );
	}

	/**
	 * Perform actions on admin init.
	 */
	public function admin_init() {
		if ( is_plugin_active( 'redis-cache/redis-cache.php' ) ) {
			deactivate_plugins( 'redis-cache/redis-cache.php' );
			update_site_option( 'spinupwp_redis_cache_disabled', true );
		}
	}
}
