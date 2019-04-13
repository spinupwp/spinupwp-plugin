<?php

namespace DeliciousBrains\SpinupWp;

use Pimple\Container;

class Plugin extends Container {

	/**
	 * @var array
	 */
	private $admin_bar_items = array();

	/**
	 *
	 */
	public function run() {
		$this['cli']   = new Cli();
		$this['cache'] = new Cache( $this, $this['cli'] );

		foreach ( $this->keys() as $key ) {
			if ( method_exists( $this[ $key ], 'init' ) ) {
				$this[ $key ]->init();
			}
		}

		add_action( 'admin_bar_menu', array( $this, 'render_admin_bar' ), 100 );
	}

	/**
	 * Add an item to the admin bar.
	 *
	 * @param string $title
	 * @param string $action
	 */
	public function add_admin_bar_item( $title, $action ) {
		$this->admin_bar_items[] = array(
			'title'  => $title,
			'action' => $action,
		);
	}

	/**
	 * Render the admin bar menu.
	 *
	 * @param $wp_admin_bar
	 */
	public function render_admin_bar( $wp_admin_bar ) {
		if ( empty( $this->admin_bar_items ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$wp_admin_bar->add_node( array(
			'id'    => 'spinupwp',
			'title' => 'SpinupWP',
		 ) );

		foreach ( $this->admin_bar_items as $item ) {
			$wp_admin_bar->add_node( array(
				'parent' => 'spinupwp',
				'id'     => strtolower( str_replace( '', '-', $item['title'] ) ),
				'title'  => $item['title'],
				'href'   => wp_nonce_url( add_query_arg( 'spinupwp_action', $item['action'], admin_url() ), $item['action'] ),
			 ) );
		}
	}

}