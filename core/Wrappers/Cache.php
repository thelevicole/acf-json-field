<?php

namespace AcfJsonField\Wrappers;

/**
 * @link  https://codex.wordpress.org/Class_Reference/WP_Object_Cache
 */
class Cache {

	public static $storageKey = 'acf_table_field';

	/**
	 * Retrieves the WP cache contents from the cache by key and group
	 *
	 * @link https://developer.wordpress.org/reference/functions/wp_cache_get/
	 *
	 * @param string $key
	 * @param bool $found
	 * @returns mixed
	 */
	public static function get( string $key, bool $found = false ) {

		$_found = false;

		$data = \wp_cache_get( $key, self::$storageKey, false, $_found );

		if ( $found ) {
			return (object)[
				'data' => $data,
				'found' => $_found
			];
		}

		return $data;
	}

	/**
	 * Saves the data to the WP cache
	 *
	 * @link https://developer.wordpress.org/reference/functions/wp_cache_set/
	 *
	 * @param string $key
	 * @param mixed $data
	 * @param int $expire When to expire the cache contents, in seconds. Default 0 (no expiration).
	 * @returns boolean
	 */
	public static function set( string $key, $data, int $expire = 0 ) {
		return \wp_cache_set( $key, $data, self::$storageKey, $expire );
	}

	/**
	 * Removes the WP cache contents matching key and group.
	 *
	 * @link https://developer.wordpress.org/reference/functions/wp_cache_delete
	 *
	 * @param string $key
	 * @returns boolean     True on successful removal, false on failure.
	 */
	public static function delete( string $key ) {
		return \wp_cache_delete( $key, self::$storageKey );
	}

	/**
	 * Conditionally get/set data to cache
	 *
	 * @param string $key
	 * @param callable $callback
	 * @param mixed $default
	 * @returns mixed
	 */
	public static function conditional( string $key, callable $callback, $default = null ) {

		// Get cached status object.
		$cached = self::get( $key, true );

		// If was found in cache, return value. Supports boolean values.
		if ( $cached->found ) {
			return $cached->data;
		}

		// Call callback, should return data to cache.
		$data = call_user_func( $callback );

		// Store data in cache.
		if ( self::set( $key, $data ) ) {
			return $data;
		}

		// Return default value.
		return $default;
	}

}
