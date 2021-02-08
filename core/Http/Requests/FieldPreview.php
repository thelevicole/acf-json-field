<?php

namespace AcfJsonField\Http\Requests;

use AcfJsonField\Contracts\Ajax;

class FieldPreview extends Ajax {

	/**
	 * The process unique request key
	 *
	 * @var string
	 */
	public $name = 'get-field-preview';

	/**
	 * If the request is accessible without being authenticated
	 *
	 * @var bool
	 */
	public $public = false;

	/**
	 * Reuqest handling
	 *
	 * @returns void
	 */
	public function handle() {
		$this->validateToken();
		$this->catchErrors();

		$input = self::input( 'text', false );

		self::sendSuccess( self::format( $input ) );
	}

	/**
	 * Helper function for standardised formatting
	 * @param string $input
	 * @param null|string $format Return a specific format
	 * @return array
	 */
	public static function format( $input, ?string $format = null ) {
		$output = apply_filters( 'the_content', $input );

		$types =  [
			'original' => $input,
			'formatted' => $output,
			'preview' => wp_trim_words( $output, 30, '...' )
		];

		if ( $format ) {
			if ( array_key_exists( $format, $types ) ) {
				return $types[ $format ];
			}

			return null;
		}

		return $types;
	}

}
