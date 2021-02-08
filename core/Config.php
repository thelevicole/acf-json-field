<?php


namespace AcfJsonField;


class Config {

	public static $config = [];

	/**
	 * Store setting value.
	 *
	 * @param string $name
	 * @param mixed $value
	 * @returns void
	 */
	public static function set( string $name, $value ) {
		static::$config[ $name ] = $value;
	}

	/**
	 * If exists, get a setting value from storage else return default.
	 * @param string $name
	 * @param mixed|null $default
	 * @returns mixed|null
	 */
	public static function get( string $name, $default = null ) {
		if ( array_key_exists( $name, static::$config ) ) {
			return static::$config[ $name ];
		}

		return $default;
	}

	/**
	 * Return all stored config vars.
	 *
	 * @returns array
	 */
	public static function all() {
		return static::$config;
	}

}
