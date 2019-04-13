<?php

namespace DeliciousBrains\SpinupWp;

use Pimple\Container;

class Plugin extends Container {

	/**
	 * Run the SpinupWP plugin.
	 */
	public function run() {
		$this['AdminBar'] = new AdminBar();
		$this['cli']      = new Cli();
		$this['cache']    = new Cache( $this['AdminBar'], $this['cli'] );

		foreach ( $this->keys() as $key ) {
			if ( method_exists( $this[ $key ], 'init' ) ) {
				$this[ $key ]->init();
			}
		}
	}
}