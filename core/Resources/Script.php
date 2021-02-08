<?php

namespace AcfJsonField\Resources;

use AcfJsonField\Utilities\Prefix;
use AcfJsonField\Utilities\Strings;

/**
 * Class Style
 *
 * @link https://developer.wordpress.org/reference/functions/wp_enqueue_script/
 *
 * @property array $variables
 * @property string $jsHandle
 */
class Script extends Asset {

	public $extension = '.js';

	/**
	 * @var boolean Whether to enqueue the script before </body> instead of in the <head>.
	 */
	public $inFooter;

	/**
	 * @var array Array of variables to be localised for this script.
	 */
	public $variables;

	/**
	 * Script constructor.
	 *
	 * @param string $path
	 * @param array $dependants
	 * @param boolean $in_footer
	 * @param array|callable $variables
	 * @param mixed|null|callable $condition
	 */
	public function __construct( string $path, array $dependants = [], bool $in_footer = true, $variables = [], $condition = null ) {
		$this->inFooter = $in_footer;
		$this->variables = $variables;
		parent::__construct( $path, $dependants, $condition );
	}

	/**
	 * Return variables, even if they are callable.
	 *
	 * @returns array
	 */
	public function getVariables() {
		return is_callable( $this->variables ) ? call_user_func( $this->variables ) : $this->variables;
	}

	/**
	 * Javascript friendly handle.
	 *
	 * @returns string
	 */
	public function getJsHandle() {
		return ( new Strings(  Prefix::prefix( $this->getHandle() . ' vars' ) ) )->camel()->get();
	}

	/**
	 * Register this asset.
	 */
	public function register() {
		wp_register_script( $this->handle, $this->url, $this->dependants, $this->version, $this->inFooter );

		if ( !empty( $this->variables ) ) {
			wp_localize_script( $this->handle, $this->jsHandle, $this->variables );
		}
	}

	/**
	 * Enqueue this asset. Register method needs to be called before.
	 *
	 * @param boolean $force Force enqueue the asset regardless of condition.
	 */
	public function enqueue( bool $force = false ) {
		if ( $this->conditionMet || $force ) {
			wp_enqueue_script( $this->handle );
		}
	}

}
