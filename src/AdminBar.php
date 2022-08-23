<?php

namespace SpinupWp;

class AdminBar {

	/**
	 * @var array
	 */
	private $items = array();

	/**
	 * @var string
	 */
	public $url;

	/**
	 * AdminBar constructor.
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
		add_action( 'admin_bar_menu', array( $this, 'render' ), 100 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Enqueue admin scripts.
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( 'spinupwp-admin', $this->url . 'assets/css/admin.css', array(), '1.0' );
	}

	/**
	 * Render the admin bar menu.
	 *
	 * @param $wp_admin_bar
	 */
	public function render( $wp_admin_bar ) {
		if ( empty( $this->items ) ) {
			return;
		}

		if ( ! current_user_can( apply_filters( 'spinupwp_purge_cache_capability', 'manage_options' ) ) ) {
			return;
		}

		$wp_admin_bar->add_node( array(
			'id'    => 'spinupwp',
			'title' => apply_filters( 'spinupwp_admin_bar_title', __( 'Cache', 'spinupwp' ) ),
		) );

		foreach ( $this->items as $item ) {
			$wp_admin_bar->add_node( array(
				'parent' => 'spinupwp',
				'id'     => strtolower( str_replace( ' ', '-', $item['title'] ) ),
				'title'  => $item['title'],
				'href'   => wp_nonce_url( add_query_arg( 'spinupwp_action', $item['action'], admin_url() ), $item['action'] ),
			) );
		}
	}

	/**
	 * Add an item to the admin bar.
	 *
	 * @param string $title
	 * @param string $action
	 */
	public function add_item( $title, $action ) {
		$this->items[] = array(
			'title'  => $title,
			'action' => $action,
		);
	}
}
