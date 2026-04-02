<?php

namespace SpinupWp;

class SubdomainUrlRewriter {

	/**
	 * @var string
	 */
	private $subdomain;

	/**
	 * @var string
	 */
	private $site_url;

	/**
	 * Init
	 *
	 * @return void
	 */
	public function init() {
		$this->subdomain = getenv( 'SPINUPWP_SUBDOMAIN' );

		if ( ! $this->subdomain || ! $this->is_subdomain_request() ) {
			return;
		}

		if ( is_multisite() && defined( 'SUBDOMAIN_INSTALL' ) && SUBDOMAIN_INSTALL ) {
			define( 'COOKIE_DOMAIN', '.' . $this->subdomain );
		}

		$this->site_url = home_url();
		$this->register_hooks();
	}

	/**
	 * Determine if the current request is via the subdomain.
	 *
	 * @return bool
	 */
	private function is_subdomain_request() {
		return isset( $_SERVER['SERVER_NAME'] )
			&& $_SERVER['SERVER_NAME'] === $this->subdomain;
	}

	/**
	 * Register hooks to rewrite URLs before saving to the database.
	 *
	 * @return void
	 */
	private function register_hooks() {
		add_filter( 'content_save_pre', array( $this, 'rewrite_urls' ) );
		add_filter( 'excerpt_save_pre', array( $this, 'rewrite_urls' ) );
		add_filter( 'pre_update_option', array( $this, 'rewrite_urls_recursive' ), 10, 1 );
	}

	/**
	 * Rewrite subdomain URLs to the configured site URL.
	 *
	 * @param string $content
	 *
	 * @return string
	 */
	public function rewrite_urls( $content ) {
		if ( ! is_string( $content ) || empty( $content ) ) {
			return $content;
		}

		return str_replace(
			array( 'https://' . $this->subdomain, 'http://' . $this->subdomain ),
			$this->site_url,
			$content
		);
	}

	/**
	 * Recursively rewrite URLs in arrays and strings.
	 *
	 * @param mixed $data
	 *
	 * @return mixed
	 */
	public function rewrite_urls_recursive( $data ) {
		if ( is_string( $data ) ) {
			return $this->rewrite_urls( $data );
		}

		if ( is_array( $data ) ) {
			foreach ( $data as $key => $value ) {
				$data[ $key ] = $this->rewrite_urls_recursive( $value );
			}
		}

		return $data;
	}
}
