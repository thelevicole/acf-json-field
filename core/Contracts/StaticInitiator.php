<?php

namespace AcfJsonField\Contracts;

trait StaticInitiator {

	/**
	 * Store local instance
	 *
	 * @var ?$this
	 */
	public static $instance = null;

	/**
	 * Create a new instance or retrieve current instance
	 *
	 * @returns $this
	 */
	public static function init() {
		if ( !static::$instance ) {
			static::$instance = new static;
		}

		return static::$instance;
	}

}
