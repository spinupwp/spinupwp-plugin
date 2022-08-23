<?php

namespace SpinupWp;

use SpinupWp\Cli\Commands;

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

		add_filter( 'spinupwp_should_use_object_cache_dropin', array( $this, 'should_use_object_cache_dropin' ) );
	}

	/**
	 * Perform actions on plugin activation.
	 */
	public static function install() {
		$plugin_path   = untrailingslashit( dirname( __DIR__ ) );
		$wpmu_dir      = untrailingslashit( WPMU_PLUGIN_DIR );

		if ( ! file_exists( $wpmu_dir . '/spinupwp-debug-log-path.php' ) ) {
			wp_mkdir_p( $wpmu_dir );
			@copy( $plugin_path . '/mu-plugins/spinupwp-debug-log-path.php', $wpmu_dir . '/spinupwp-debug-log-path.php' );
		}

		self::update_object_cache_dropin();
	}

	/**
	 * Perform the update of the object-cache.php drop-in
	 *
	 * @return bool
	 */
	public static function update_object_cache_dropin() {
		if ( ! apply_filters( 'spinupwp_should_use_object_cache_dropin', true ) ) {
			return false;
		}

		$wpcontent_dir = untrailingslashit( WP_CONTENT_DIR );
		$plugin_path   = untrailingslashit( dirname( __DIR__ ) );

		$existing_backed_up = false;
		if ( file_exists( $wpcontent_dir . '/object-cache.php' ) ) {
			$existing_backed_up = @copy( $wpcontent_dir . '/object-cache.php', $wpcontent_dir . '/object-cache.php.bak' );
			@unlink( $wpcontent_dir . '/object-cache.php' );
		}

		$result = @copy( $plugin_path . '/drop-ins/object-cache.php', $wpcontent_dir . '/object-cache.php' );

		if ( $existing_backed_up ) {
			if ( $result ) {
				@unlink( $wpcontent_dir . '/object-cache.php.bak' );
			} else {
				@rename( $wpcontent_dir . '/object-cache.php.bak', $wpcontent_dir . '/object-cache.php' );
			}
		}

		if ( $result && function_exists( 'wp_cache_flush' ) ) {
			wp_cache_flush();
		}

		return $result;
	}

	/**
	 * @param bool $check
	 *
	 * @return bool
	 */
	public function should_use_object_cache_dropin( $check ) {
		if ( defined( 'RedisCachePro\Version' ) ) {
			// Don't use our object-cache.php drop-in if the site is using the Object Cache Pro plugin
			return false;
		}

		return $check;
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
