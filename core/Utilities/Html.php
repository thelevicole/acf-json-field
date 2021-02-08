<?php

namespace AcfJsonField\Utilities;

class Html {

	/**
	 * Tidy html classes
	 *
	 * @param string|array $class
	 * @param string $prefix
	 * @returns string|null
	 */
	public static function escClasses( $classes, string $prefix = '' ): ?string {

		/**
		 * Convert string to array.
		 */
		if ( is_string( $classes ) ) {
			$classes = preg_split( '/\s/', $classes );
		}

		/**
		 * Bail early if classes is anything but an array.
		 */
		if ( !is_array( $classes ) ) {
			return null;
		}

		// For each item in `$classes` array
		$classes = array_map( function( $item ) use ( $prefix ) {

			// Check if item has multiple classes in one
			$items = preg_split( '/\s+/' , $item );

			// Sanitize prefixed values for DOM
			$items = array_map( function( $value ) use ( $prefix ) {
				return sanitize_html_class( $prefix . $value );
			}, $items );

			// Return inlined string
			return implode( ' ', $items );

		}, array_filter( $classes ) );

		// Implode values into a single string
		$classes = implode( ' ', array_filter( $classes ) );

		// Remove double white space
		$classes = preg_replace( '/\s{2,}/', ' ', $classes );

		// Trim any extra white space
		$classes = trim( $classes );

		return $classes;
	}

	/**
	 * Escape and inline HTML attribute
	 *
	 * @param array $args,...
	 * @returns string
	 */
	public static function escAttributes( ...$args ): string {

		$return = '';

		$attributes = array_merge_recursive( ...$args );

		foreach ( $attributes as $key => $value ) {

			// Skip attribute if value is empty
			if ( empty( $value ) ) {
				continue;
			}

			// Handle custom class inliner
			if ( $key === 'class' ) {
				$value = self::escClasses( $value );
			}

			// Value is string
			else if ( is_string( $value ) ) {
				$value = trim( $value );
			}

			// Value is boolean
			else if ( is_bool( $value ) ) {
				$value = $value ? 1 : 0;
			}

			// Value is array or object
			else if ( is_array( $value ) || is_object( $value ) ) {
				$value = json_encode( $value );
			}

			// append
			$return .= esc_attr( $key ) . '="' . esc_attr( $value ) . '" ';

		}

		return trim( $return );
	}

}
