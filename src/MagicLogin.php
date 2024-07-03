<?php

namespace SpinupWp;

use Exception;
use WP_User;

class MagicLogin {

	/**
	 * Init
	 *
	 * @return void
	 */
	public function init() {
		if (
			is_admin()
			|| ( defined( 'DOING_AJAX' ) && DOING_AJAX )
			|| ( defined( 'DOING_CRON' ) && DOING_CRON )
			|| ( defined( 'WP_CLI' ) && WP_CLI )
			|| ( defined( 'WP_INSTALLING' ) && WP_INSTALLING ) ) {
			return;
		}

		if ( ! $this->is_login_request() ) {
			return;
		}

		add_action( 'plugins_loaded', array( $this, 'handle_request' ) );
	}

	/**
	 * Handle magic login request.
	 */
	public function handle_request() {
		$secret = $this->get_login_secret();

		if ( ! $this->has_valid_signature( $secret ) ) {
			$this->error('Invalid Signature', 'Your login link is not valid. Please try again.');
		}

		if ( $this->has_exipred() ) {
			$this->error('Link Expired', 'Your login link has expired. Please try again.');
		}

		$user = $this->retrieve_user();

		if ( ! $user ) {
			$this->error('User Not Found', 'No such user with that login exists.');
		}

		wp_set_auth_cookie( $user->ID );
		wp_safe_redirect( admin_url() );
		exit;
	}

	/**
	 * Determine if this is a magic login request.
	 *
	 * @return bool
	 */
	protected function is_login_request() {
		$query = $_SERVER['QUERY_STRING'];

		if ( empty( $query ) ) {
			return false;
		}

		parse_str( $query, $parameters );

		if ( empty( $parameters['spinupwp_signature'] ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Get the login secret.
	 *
	 * @return string
	 * @throws Exception
	 */
	protected function get_login_secret() {
		$secret_path = getenv( 'HOME' ) . DIRECTORY_SEPARATOR . '.spinupwp-login.secret';

		if ( ! file_exists( $secret_path ) ) {
			throw new Exception( 'Secret not found' );
		}

		$secret = file_get_contents( $secret_path );

		if ( ! $secret ) {
			throw new Exception( 'Cannot read secret' );
		}

		return trim( $secret );
	}

	/**
	 * Determine if the signature is valid.
	 *
	 * @return bool
	 */
	protected function has_valid_signature( string $secret ) {
		parse_str( $_SERVER['QUERY_STRING'], $parameters );

		$query_signature = array_pop( $parameters );

		$query     = http_build_query( $parameters );
		$url       = home_url() . "?{$query}";
		$signature = hash_hmac( 'sha256', $url, $secret );

		return hash_equals( $signature, $query_signature );
	}

	/**
	 * Determine if the signature has expired.
	 *
	 * @return bool
	 */
	protected function has_exipred() {
		parse_str( $_SERVER['QUERY_STRING'], $parameters );

		if ( empty( $parameters['expires'] ) ) {
			return true;
		}

		return time() > (int) $parameters['expires'];
	}

	/**
	 * Retrieve the user.
	 *
	 * @return WP_User|false
	 */
	protected function retrieve_user() {
		parse_str( $_SERVER['QUERY_STRING'], $parameters );
		$user_name = sanitize_user( $parameters['user'] );

		$user = get_user_by( 'login', $user_name );

		if ( ! $user && strpos( $user_name, '@' ) ) {
			$user = get_user_by( 'email', $user_name );
		}

		return $user;
	}

	/**
	 * Display error and die.
	 */
	protected function error($title, $body) {
		wp_die( "<h1>{$title}</h1>\n<p>{$body}</p>" );
	}
}