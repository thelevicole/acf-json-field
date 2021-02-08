<?php

namespace AcfJsonField\Resources;

/**
 * Class Style
 *
 * @link https://developer.wordpress.org/reference/functions/wp_enqueue_style/
 */
class Style extends Asset {

	public $extension = '.css';

	/**
	 * @var string The media for which this stylesheet has been defined.
	 */
	public $media;

	/**
	 * Style constructor.
	 *
	 * @param string $path
	 * @param array $dependants
	 * @param mixed|null|callable $condition
	 * @param string $media
	 */
	public function __construct( string $path, array $dependants = [], $condition = null, string $media = 'all' ) {
		$this->media = $media;
		parent::__construct( $path, $dependants, $condition );
	}

	/**
	 * Register this asset.
	 */
	public function register() {
		wp_register_style( $this->handle, $this->url, $this->dependants, $this->version, $this->media );
	}

	/**
	 * Enqueue this asset. Register method needs to be called before.
	 *
	 * @param boolean $force Force enqueue the asset regardless of condition.
	 */
	public function enqueue( bool $force = false ) {
		if ( $this->conditionMet || $force ) {
			wp_enqueue_style( $this->handle );
		}
	}
}
