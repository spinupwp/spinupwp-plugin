<?php

namespace DeliciousBrains\SpinupWp;

class Cache {
	/**
	 * @var Plugin
	 */
	private $plugin;

	/**
	 * Cache constructor.
	 *
	 * @param Plugin $plugin
	 */
	public function __construct( Plugin $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Init
	 */
	public function init() {
		//
	}
}