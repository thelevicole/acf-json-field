<?php

namespace AcfJsonField\Resources;

use Exception;
use AcfJsonField\Contracts\Prefixer;
use AcfJsonField\Utilities\Constants;
use AcfJsonField\Utilities\Strings;

/**
 * @property string $handle
 * @property string $version
 * @property string $path
 * @property string $url
 * @property mixed $conditionMet
 */
class Asset {

	use Prefixer;

	/**
	 * @var string Magicfied. Asset handle.
	 */
	public $_handle;

	/**
	 * @var string Magicfied. Asset relative path.
	 */
	public $_path;

	/**
	 * @var string Magicfied. Version string, theme version used as default.
	 */
	public $_version;

	/**
	 * @var string|null The extension of the asset file.
	 */
	public $extension = null;

	/**
	 * @var array Array of asset handles before this asset is enqueued.
	 */
	public $dependants;

	/**
	 * @var mixed|null|callable This is check for a positive value before enqueing asset.
	 */
	public $condition;

	/**
	 * @var int The priority the register hook should be called.
	 */
	public $registerPriority = 10;

	/**
	 * @var string The WordPress register asset hook name.
	 */
	public $registerHook = 'wp_enqueue_scripts';

	/**
	 * @var int The priority the enqueue hook should be called.
	 */
	public $enqueuePriority = 10;

	/**
	 * @var string The WordPress enqueue asset hook name.
	 */
	public $enqueueHook = 'wp_enqueue_scripts';

	/**
	 * @var array Track generated handles.
	 */
	public static $handles = [];

	/**
	 * Asset constructor.
	 *
	 * @param string $path
	 * @param array $dependants
	 * @param mixed|null|callable $condition
	 */
	function __construct( string $path, array $dependants = [], $condition = null ) {
		$this->path = $path;
		$this->dependants = $dependants;
		$this->condition = $condition;
	}

	/**
	 * Get property directly from WP_Post object.
	 *
	 * @param $name
	 * @returns mixed
	 * @throws Exception
	 */
	public function __get( $name ) {

		/**
		 * Generate a magic attribute method name from getter key. Inspired by Laravel.
		 *
		 * @example handle -> getHandle()
		 * @example version -> getVersion()
		 */
		$property_method = ( new Strings( "get $name" ) )->camel()->get();

		/**
		 * See if we have a magic attribute method.
		 */
		if ( method_exists( $this, $property_method ) ) {
			return call_user_func( [ $this, $property_method ] );
		}

		throw new Exception( "Property $name is not defined" );
	}

	/**
	 * Set magic values.
	 *
	 * @param $name
	 * @param $value
	 * @throws Exception
	 */
	public function __set( $name, $value ) {
		$property = '_' . $name;

		/**
		 * Generate a magic attribute method name from setter key. Inspired by Laravel.
		 *
		 * @example handle -> setHandle()
		 * @example version -> setVersion()
		 */
		$property_method = ( new Strings( "set $name" ) )->camel()->get();

		if ( property_exists( $this, $property ) ) {
			$this->$property = $value;
		} else if ( method_exists( $this, $property_method ) ) {
			call_user_func( [ $this, $property_method ] );
		} else {
			throw new Exception( "Property $name is not defined" );
		}
	}

	/**
	 * Alter callable hooks for admin registering/enqueueing.
	 */
	public function loadForAdmin() {
		$this->enqueueHook = 'admin_enqueue_scripts';
		$this->registerHook = 'admin_enqueue_scripts';
	}

	/**
	 * Return asset handle or generate one based on the path.
	 *
	 * @returns string
	 */
	public function getHandle() {

		$handle = $this->_handle;

		if ( !$handle ) {
			$handle = sanitize_title( self::prefix( basename( $this->path, $this->extension ) ) );

			$hamdle_tracker = $handle . $this->extension;

			if ( !array_key_exists( $handle, self::$handles ) ) {
				self::$handles[ $hamdle_tracker ] = 0;
			}

			// Count number of times this name has appeared.
			self::$handles[ $hamdle_tracker ]++;

			if ( self::$handles[ $hamdle_tracker ] > 1 ) {
				$handle .= '-' . self::$handles[ $hamdle_tracker ];
			}
		}

		return $handle;
	}

	/**
	 * Get asset version string.
	 *
	 * @returns string
	 */
	public function getVersion() {

		$version = $this->_version;

		if ( !$version ) {
			$version = Constants::get( 'version' );
		}

		return $version;
	}

	/**
	 * Return a full url of the asset.
	 *
	 * @returns string
	 */
	public function getUrl() {
		return rtrim( Constants::get( 'URL' ), '/' ) . '/' . ltrim( $this->_path, '/' );
	}

	/**
	 * Return a full local path of the asset.
	 *
	 * @returns string
	 */
	public function getPath() {
		return rtrim( Constants::get( 'PATH' ), DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR . ltrim( $this->_path, DIRECTORY_SEPARATOR );
	}

	/**
	 * Check if condition is met.
	 *
	 * @returns bool|mixed|null
	 */
	public function getConditionMet() {

		if ( !is_null( $this->condition ) ) {
			if ( is_callable( $this->condition ) ) {
				return call_user_func( $this->condition );
			}

			return $this->condition;
		}

		return true;
	}
}
