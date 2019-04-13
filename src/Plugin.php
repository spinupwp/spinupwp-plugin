<?php

namespace DeliciousBrains\SpinupWp;

use Pimple\Container;

class Plugin extends Container {

	/**
	 * Run the SpinupWP plugin.
	 */
	public function run() {
		$this['AdminBar'] = new AdminBar();
		$this['Cli']      = new Cli();
		$this['Cache']    = new Cache( $this['AdminBar'], $this['Cli'] );

		foreach ( $this->keys() as $key ) {
			if ( method_exists( $this[ $key ], 'init' ) ) {
				$this[ $key ]->init();
			}
		}
	}
}