<?php

namespace DeliciousBrains\SpinupWp;

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
			add_action( 'network_admin_notices', array( $this, 'show_mail_notice' ) );
		} else {
			add_action( 'admin_notices', array( $this, 'show_mail_notice' ) );
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
			wp_die( -1, 403 );
		}

		update_site_option( 'spinupwp_mail_notice_dismissed', true );
	}

	/**
	 * Show a notice about configuring mail.
	 */
	public function show_mail_notice() {
		if ( ! current_user_can( 'manage_options' ) || get_site_option( 'spinupwp_mail_notice_dismissed' ) ) {
			return;
		}

		$msg   = __( 'Your site is ready to go! You will need to set up email if you wish to send outgoing emails from this site.', 'spinupwp' );
		$link  = sprintf( '<a href="%s">%s &raquo;</a>', 'https://spinupwp.com/doc/setting-up-transactional-email-wordpress/', __( 'More info', 'spinupwp' ) );
		$nonce = wp_create_nonce( 'dismiss-notice' );
		echo "<div class=\"spinupwp notice notice-success is-dismissible\" data-nonce=\"{$nonce}\"><p><strong>SpinupWP</strong> — {$msg} {$link}</p></div>";
	}
}