<?php

namespace SpinupWp;

class AdminNotices {

	/**
	 * @var string
	 */
	public $url;

	/**
	 * AdminNotices constructor.
	 *
	 * @param string $url
	 */
	public function __construct( $url ) {
		$this->url = $url;
	}

	/**
	 * Init
	 */
	public function init() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'wp_ajax_spinupwp_dismiss_notice', array( $this, 'ajax_dismiss_notice' ) );

		if ( is_multisite() ) {
			add_action( 'network_admin_notices', array( $this, 'show_notices' ) );
		} else {
			add_action( 'admin_notices', array( $this, 'show_notices' ) );
		}
	}

	/**
	 * Enqueue admin scripts.
	 */
	public function enqueue_scripts() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		wp_enqueue_script( 'spinupwp-dismiss', $this->url . 'assets/js/dismiss-notice.js', array( 'jquery' ), '1.0' );
	}

	/**
	 * Handle AJAX request to dismiss notice.
	 */
	public function ajax_dismiss_notice() {
		if ( ! check_ajax_referer( 'dismiss-notice', 'nonce', false ) || ! current_user_can( 'manage_options' ) ) {
			wp_die( - 1, 403 );
		}

		$notice = filter_var( $_POST['notice'], FILTER_SANITIZE_STRING );

		update_site_option( "spinupwp_{$notice}_notice_dismissed", true );
	}

	/**
	 * Show notices.
	 */
	public function show_notices() {
		$this->show_mail_notice();
		$this->show_redis_cache_disabled_notice();
		$this->show_object_cache_dropin_updated_notice();
	}

	/**
	 * Show a notice about configuring mail.
	 */
	public function show_mail_notice() {
		if ( ! current_user_can( 'manage_options' ) || get_site_option( 'spinupwp_mail_notice_dismissed' ) ) {
			return;
		}

		$msg   = __( 'Your site is ready to go! You will need to set up email if you wish to send outgoing emails from this site.', 'spinupwp' );
		$link  = sprintf( '<a href="%s">%s &raquo;</a>', 'https://spinupwp.com/doc/setting-up-transactional-email-wordpress/?utm_campaign=support%2Bdocs&utm_source=SpinupWP%2BMU&utm_medium=insideplugin', __( 'More info', 'spinupwp' ) );
		$nonce = wp_create_nonce( 'dismiss-notice' );
		echo "<div class=\"spinupwp notice notice-success is-dismissible\" data-nonce=\"{$nonce}\" data-notice=\"mail\"><p><strong>SpinupWP</strong> — {$msg} {$link}</p></div>";
	}

	/**
	 * Show a notice about Redis Object Cache plugin being disabled.
	 */
	public function show_redis_cache_disabled_notice() {
		if ( ! current_user_can( 'manage_options' ) || ! get_site_option( 'spinupwp_redis_cache_disabled' ) || get_site_option( 'spinupwp_redis_cache_disabled_notice_dismissed' ) ) {
			return;
		}

		$msg   = __( 'The Redis Object Cache plugin has been deactivated and can be removed. The SpinupWP plugin now handles clearing the Redis object cache.', 'spinupwp' );
		$nonce = wp_create_nonce( 'dismiss-notice' );
		echo "<div class=\"spinupwp notice notice-success is-dismissible\" data-nonce=\"{$nonce}\" data-notice=\"redis_cache_disabled\"><p><strong>SpinupWP</strong> — {$msg}</p></div>";
	}

	/**
	 * Show a notice when the object-cache.php drop-in has been updated.
	 */
	public function show_object_cache_dropin_updated_notice() {
		$wpcontent_dir = untrailingslashit( WP_CONTENT_DIR );

		if ( file_exists( $wpcontent_dir . '/object-cache.php' ) ) {
			$plugin_path = untrailingslashit( dirname( __DIR__ ) );

			$dropin = get_plugin_data( $wpcontent_dir . '/object-cache.php' );
			$plugin = get_plugin_data( $plugin_path . '/drop-ins/object-cache.php' );

			if ( $dropin['PluginURI'] !== $plugin['PluginURI'] ) {
				return;
			}

			if ( version_compare( $dropin['Version'], $plugin['Version'], '<' ) ) {
				$result = Plugin::update_object_cache_dropin();

				if ( $result ) {
					$msg = __( 'Object cache drop-in updated.', 'spinupwp' );
					echo "<div class=\"spinupwp notice notice-success\"><p><strong>SpinupWP</strong> — {$msg}</p></div>";
				} else {
					$msg = __( 'Object cache drop-in could not be updated.', 'spinupwp' );
					echo "<div class=\"spinupwp notice notice-error\"><p><strong>SpinupWP</strong> — {$msg}</p></div>";
				}
			}
		}
	}
}
