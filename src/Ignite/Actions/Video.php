<?php

namespace Ignite\Actions;

use Ignite\Action;

class Video extends ActionBuffer {

	/**
	 * @param string $video
	 * @return Action
	 */
	public static function playModal($video) {
		$action = new Action('pv:', func_get_args());
		self::$actionBuffer[] = $action;

		return $action;
	}


} 