<?php

namespace Ignite\Actions;

use Ignite\Action;

class Search extends ActionBuffer {

	/**
	 * @return Action
	 */
	public static function display() {
		$action = new Action('search');
		self::$actionBuffer[] = $action;

		return $action;
	}

} 