<?php

namespace AcfJsonField\Resources;

class AdminEnqueuer extends Enqueuer {

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
		$script = parent::addScript( $path, $dependants, $in_footer, $variables, $condition );
		$script->loadForAdmin();
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
		$style = parent::addStyle( $path, $dependants, $condition, $media );
		$style->loadForAdmin();
		return $style;
	}

}
