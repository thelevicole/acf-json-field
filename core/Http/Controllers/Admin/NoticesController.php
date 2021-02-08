<?php

namespace AcfJsonField\Admin;

use AcfJsonField\Http\Controllers\BaseController;

class NoticesController extends BaseController {

	/**
	 * @var array Store notices to display to the user.
	 */
	public static $notices = [];

	/**
	 * NoticesController constructor.
	 */
	function __construct() {
		add_action( 'admin_notices', [ self::class, 'renderNotices' ] );
	}

	/**
	 * Render any admin notices.
	 *
	 * @link https://codex.wordpress.org/Plugin_API/Action_Reference/admin_notices
	 * @returns void
	 */
	public static function renderNotices() {
		if ( $notices = static::getNotices() ) {
			foreach ( $notices as $notice ) {
				$notice->render();
			}
		}
	}

	/**
	 * Add notice data.
	 *
	 * @param string $text
	 * @param array $args
	 * @returns Notice
	 */
	public static function addNotice( string $text, array $args = [] ) {
		$notice = new Notice( $text, $args );
		static::$notices[] = $notice;
		return $notice;
	}

	/**
	 * Add success notice.
	 *
	 * @param string $text
	 * @param array	$args
	 * @returns Notice
	 */
	public static function addSuccessNotice( string $text, array $args = [] ) {
		$args[ 'type' ] = 'success';
		return static::addNotice( $text, $args );
	}

	/**
	 * Add warning notice.
	 *
	 * @param string $text
	 * @param array	$args
	 * @returns Notice
	 */
	public static function addWarningNotice( string $text, array $args = [] ) {
		$args[ 'type' ] = 'warning';
		return static::addNotice( $text, $args );
	}

	/**
	 * Add error notice.
	 *
	 * @param string $text
	 * @param array	$args
	 * @returns Notice
	 */
	public static function addErrorNotice( string $text, array $args = [] ) {
		$args[ 'type' ] = 'error';
		return static::addNotice( $text, $args );
	}

	/**
	 * Return an array of registered notices.
	 *
	 * @returns array
	 */
	public static function getNotices() {
		return static::$notices;
	}
}
