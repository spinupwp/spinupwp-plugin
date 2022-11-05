<?php

namespace SpinupWp;

class SiteHealth {

	/**
	 * Init
	 */
	public function init() {
		add_filter( 'site_status_tests', array( $this, 'filter_site_health_checks' ) );
		add_filter( 'site_status_page_cache_supported_cache_headers', array( $this, 'filter_page_cache_headers' ) );
	}

	/**
	 * Add SpinupWP specific tests to the site health screen.
	 *
	 * @param array $tests
	 *
	 * @return array
	 */
	public function filter_site_health_checks( $tests ) {
		$tests['direct']['debug_enabled'] = array(
			'label' => __( 'Debugging enabled' ),
			'test'  => array( $this, 'test_debug_mode' )
		);

		return $tests;
	}

	/**
	 * Test if debug information is enabled.
	 *
	 * Copied from `WP_Site_Health::get_test_is_in_debug_mode` but removed
	 * warning about `WP_DEBUG_LOG` constant because SpinupWP denies access
	 * to all *.log files.
	 *
	 * @return array The test results.
	 */
	public function test_debug_mode() {
		$result = array(
			'label'       => __( 'Your site is not set to output debug information' ),
			'status'      => 'good',
			'badge'       => array(
				'label' => __( 'Security' ),
				'color' => 'blue',
			),
			'description' => sprintf(
				'<p>%s</p>',
				__( 'Debug mode is often enabled to gather more details about an error or site failure, but may contain sensitive information which should not be available on a publicly available website.' )
			),
			'actions'     => sprintf(
				'<p><a href="%s" target="_blank" rel="noopener noreferrer">%s <span class="screen-reader-text">%s</span><span aria-hidden="true" class="dashicons dashicons-external"></span></a></p>',
				/* translators: Documentation explaining debugging in WordPress. */
				esc_url( __( 'https://wordpress.org/support/article/debugging-in-wordpress/' ) ),
				__( 'Read about debugging in WordPress.' ),
				/* translators: accessibility text */
				__( '(opens in a new tab)' )
			),
			'test'        => 'is_in_debug_mode',
		);

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG && defined( 'WP_DEBUG_DISPLAY' ) && WP_DEBUG_DISPLAY ) {
			$result['label'] = __( 'Your site is set to display errors to site visitors' );

			$result['status'] = 'critical';

			$result['description'] .= sprintf(
				'<p>%s</p>',
				sprintf(
				/* translators: 1: WP_DEBUG_DISPLAY, 2: WP_DEBUG */
					__( 'The value, %1$s, has either been enabled by %2$s or added to your configuration file. This will make errors display on the front end of your site.' ),
					'<code>WP_DEBUG_DISPLAY</code>',
					'<code>WP_DEBUG</code>'
				)
			);
		}

		return $result;
	}

	/**
	 * Filters the list of cache headers supported by core.
	 *
	 * @param array $cache_headers
	 *
	 * @return array
	 */
	public function filter_page_cache_headers( $cache_headers ) {
		$cache_headers['fastcgi-cache'] = static function ( $header_value ) {
			return in_array( strtolower( $header_value ), array(
				'hit',
				'miss',
				'bypass',
			), true );
		};

		return $cache_headers;
	}
}