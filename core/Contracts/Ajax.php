<?php

namespace AcfJsonField\Contracts;

use AcfJsonField\Contracts\StaticInitiator;
use AcfJsonField\Contracts\Errorable;
use WP_Error;

/**
 *
 */
class Ajax {

	use StaticInitiator;
	use Errorable;

	/**
	 * Prefix used for registering request action
	 *
	 * @var string
	 */
	public static $prefix = 'AcfJsonField_';

	/**
	 * Action string used for verifying request
	 *
	 * @var string
	 */
	public static $actionToken = 'acf-table-field';

	/**
	 * A unique request name used when calling the correct hook
	 *
	 * @var string
	 */
	public $name;

	/**
	 * Set to true if ajax request can be made without authentication
	 *
	 * @var string
	 */
	public $public = false;

	/**
	 * Register ajax action
	 *
	 * @returns void
	 */
	public function register() {
		add_action( 'wp_ajax_' . static::requestName(), [ $this, 'handle' ] );

		if ( $this->public ) {
			add_action( 'wp_ajax_nopriv_' . static::requestName(), [ $this, 'handle' ] );
		}
	}

	/**
	 * Request handler
	 *
	 * @returns void
	 */
	public function handle() {
		\wp_die( __( 'Request handling failed.', 'skape' ) );
	}

	/**
	 * Conditionally return error response if errors exist
	 *
	 * @returns mixed|void
	 */
	public function catchErrors() {
		if ( $this->hasErrors() ) {
			return self::sendError( $this->getErrors() );
		}
	}

	/**
	 * Add generic error if nonce token is invalid
	 *
	 * @returns void
	 */
	public function validateToken() {
		if ( !self::validToken() ) {
			$this->addError( 'csrf', __( 'Session timedout.', 'skape' ) );
		}
	}

	/**
	 * Get the full prefixed request name
	 *
	 * @returns string
	 */
	public static function requestName() {
		return static::$prefix . static::init()->name;
	}

	/**
	 * Get sanitized value from request
	 *
	 * @param string $name Name of field in request
	 * @param string|null $filter Name of function to clean field
	 * @returns mixed
	 */
	public static function input( string $name, ?string $filter = 'sanitize_text_field' ) {
		$value = !empty( $_REQUEST[ $name ] ) ? $_REQUEST[ $name ] : null;

		if ( $filter ) {
			$value = call_user_func_array( $filter, [ $value ] );
		}

		return $value;
	}

	/**
	 * Generate a request token
	 *
	 * @link  https://developer.wordpress.org/reference/functions/wp_create_nonce/
	 *
	 * @returns string
	 */
	public static function generateToken() {
		return \wp_create_nonce( static::$actionToken );
	}

	/**
	 * Check if request is valid
	 *
	 * @link  https://developer.wordpress.org/reference/functions/wp_verify_nonce/
	 *
	 * @returns boolean
	 */
	public static function validToken() {
		return \wp_verify_nonce( self::input( 'csrf' ), static::$actionToken );
	}

	/**
	 * Send JSON error response
	 *
	 * @param $data
	 * @returns mixed
	 */
	public static function sendError( $data ) {
		return \wp_send_json_error( $data );
	}

	/**
	 * Send JSON success response
	 *
	 * @param $data
	 * @returns mixed
	 */
	public static function sendSuccess( $data ) {
		return \wp_send_json_success( $data );
	}

}
