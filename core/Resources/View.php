<?php

namespace AcfJsonField\Resources;

use AcfJsonField\Utilities\Constants;
use AcfJsonField\Wrappers\Options;

class View {

	public static $path = 'views';

	public $name = null;
	public $vars = [];

	public function __construct( string $name, array $vars = [] ) {
		$this->name = $name;
		$this->vars = $vars;
	}

	/**
	 * Get the full absolute view path
	 *
	 * @returns string
	 */
	public function getPath() {
		$path = Constants::get( 'PATH' ) . DIRECTORY_SEPARATOR . trim( self::$path, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR . preg_replace( '/\.php$/', '', $this->name ) . '.php';
		return file_exists( $path ) ? $path : null;
	}

	/**
	 * Add a variable to the var array
	 *
	 * @param string $name
	 * @param mixed $value
	 * @returns void
	 */
	public function addVar( string $name, $value ) {
		$this->vars[ $name ] = $value;
	}

	/**
	 * Remove a variable from the var array
	 *
	 * @param string $name
	 * @returns void
	 */
	public function removeVar( string $name ) {
		unset( $this->vars[ $name ] );
	}

	/**
	 * Render the view if exists
	 *
	 * @returns void
	 */
	public function render() {
		if ( $this->getPath() ) {
			extract( $this->vars );
			require $this->getPath();
		}
	}

}
