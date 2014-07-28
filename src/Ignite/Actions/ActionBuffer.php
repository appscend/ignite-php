<?php
namespace Ignite\Actions;

use Ignite\Action;

abstract class ActionBuffer {

	/**
	 * @var Action[]
	 */
	protected static $actionBuffer = [];

	public static function getAndClearBuffer() {
		$copy = self::$actionBuffer;
		self::$actionBuffer = [];

		return $copy;
	}

} 