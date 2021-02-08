<?php

namespace AcfJsonField\Resources;

use AcfJsonField\Contracts\Prefixer;

class Enqueuer {

	use Prefixer;

	public $scripts = [];
	public $styles = [];

	/**
	 * Return all registered scripts. Or script attributes.
	 *
	 * @param null|string $attr
	 * @return array
	 */
	public function getScripts( ?string $attr = null ) {
		$scripts = $this->scripts;

		if ( $attr ) {
			$scripts = array_filter( array_map( function( $script ) use ( $attr ) {
				return !empty( $script->$attr ) ? $script->$attr : null;
			}, $scripts ) );
		}

		return $scripts;
	}

	/**
	 * Return all registered styles. Or style attributes.
	 *
	 * @param null|string $attr
	 * @return array
	 */
	public function getStyles( ?string $attr = null ) {
		$styles = $this->styles;

		if ( $attr ) {
			$styles = array_filter( array_map( function( $style ) use ( $attr ) {
				return !empty( $style->$attr ) ? $style->$attr : null;
			}, $styles ) );
		}

		return $styles;
	}

	/**
	 * Add a JS asset to this enqueuer.
	 *
	 * @param string $path
	 * @param array $dependants
	 * @param boolean $in_footer
	 * @param mixed|null|callable $condition
	 * @returns Script
	 */
	public function addScript( string $path, array $dependants = [], bool $in_footer = true, array $variables = [], $condition = null ) {
		$script = new Script( $path, $dependants, $in_footer, $variables, $condition );
		$this->scripts[] = $script;
		return $script;
	}

	/**
	 * Add a CSS asset to this enqueuer.
	 *
	 * @param string $path
	 * @param array $dependants
	 * @param mixed|null|callable $condition
	 * @param string $media
	 * @returns Style
	 */
	public function addStyle( string $path, array $dependants = [], $condition = null, string $media = 'all' ) {
		$style = new Style( $path, $dependants, $condition, $media );
		$this->styles[] = $style;
		return $style;
	}

	/**
	 * Method for registering assets.
	 *
	 * @param boolean $call_action
	 */
	public function register( bool $call_action = true ) {

		// Register each asset from each group
		foreach ( [ 'scripts', 'styles' ] as $group ) {
			foreach ( $this->$group as $asset ) {
				if ( $call_action ) {
					add_action( $asset->registerHook, function () use ( $asset ) {
						$asset->register();
					}, $asset->registerPriority );
				} else {
					$asset->register();
				}
			}
		}

	}

	/**
	 * Method for enqueueing registered assets.
	 *
	 * @param boolean $call_action
	 * @param boolean $force Override asset conditions.
	 */
	public function enqueue( bool $call_action = true, bool $force = false ) {

		//Eenqueue each asset from each group
		foreach ( [ 'scripts', 'styles' ] as $group ) {
			foreach ( $this->$group as $asset ) {
				if ( $call_action ) {
					add_action( $asset->registerHook, function () use ( $asset, $force ) {
						$asset->enqueue( $force );
					}, $asset->registerPriority );
				} else {
					$asset->enqueue( $force );
				}
			}
		}

	}

	/**
	 * Run both the register and enqueue methods.
	 *
	 * @param boolean $call_action
	 * @param boolean $force Override asset conditions.
	 */
	public function registerAndEnqueue( bool $call_action = true, bool $force = false ) {
		$this->register( $call_action );
		$this->enqueue( $call_action, $force );
	}

}
