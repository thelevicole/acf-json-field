<?php

namespace AcfJsonField\Wrappers;

use AcfJsonField\Contracts\Prefixer;
use AcfJsonField\Utilities\Constants;


class Hooks {

	use Prefixer;

	public static $prefixOperator = '/';

	/**
	 * @var array Store a local log of called and registered hooks.
	 */
	public static $log = [];

	/**
	 * Log a hook tag.
	 *
	 * @param string $group
	 * @param string $tag
	 */
	private static function log( string $group, string $tag ) {
		if ( Constants::get( 'debug', false ) ) {
			if ( !array_key_exists( $group, self::$log ) ) {
				self::$log[ $group ] = [];
			}

			if ( !array_key_exists( $tag, self::$log[ $group ] ) ) {
				self::$log[ $group ][ $tag ] = 0;
			}

			self::$log[ $group ][ $tag ]++;
		}
	}

	/**
	 * Get the full log or filter by group.
	 *
	 * @param string|null $group
	 * @returns array|null
	 */
	public static function getLog( ?string $group = null ) {
		if ( $group ) {
			if ( array_key_exists( $group, self::$log ) ) {
				return self::$log[ $group ];
			}

			return null;
		}
		return self::$log;
	}

	/**
	 * Add WordPress filter
	 *
	 * @link https://developer.wordpress.org/reference/functions/add_filter/
	 *
	 * @param string $tag
	 * @param callable $callback
	 * @param integer $priority
	 * @param integer $accepted_args
	 */
	public static function addFilter( string $tag, $callback, int $priority = 10, int $accepted_args = 1 ) {
		$tag = self::prefix( $tag );
		self::log( 'add_filter', $tag );
		add_filter( $tag, $callback, $priority, $accepted_args );
	}


	/**
	 * Apply registered filters with prefix
	 *
	 * @link https://developer.wordpress.org/reference/functions/apply_filters/
	 *
	 * @param string $tag
	 * @param mixed $value
	 * @returns mixed
	 */
	public static function applyFilters( string $tag, $value, ...$args ) {
		$tag = self::prefix( $tag );
		self::log( 'apply_filters', $tag );
		return apply_filters( $tag, $value, ...$args );
	}

	/**
	 * Add WordPress action
	 *
	 * @link https://developer.wordpress.org/reference/functions/add_action/
	 *
	 * @param string $tag
	 * @param callable $callback
	 * @param integer $priority
	 * @param integer $accepted_args
	 */
	public static function addAction( string $tag, $callback, int $priority = 10, int $accepted_args = 1 ) {
		$tag = self::prefix( $tag );
		self::log( 'add_action', $tag );
		add_action( $tag, $callback, $priority, $accepted_args );
	}

	/**
	 * Perform WordPress action
	 *
	 * @link https://developer.wordpress.org/reference/functions/do_action/
	 *
	 * @param string $tag
	 * @param mixed $args
	 */
	public static function doAction( string $tag, ...$args ) {
		$tag = self::prefix( $tag );
		self::log( 'do_action', $tag );
		do_action( $tag, ...$args );
	}

}
