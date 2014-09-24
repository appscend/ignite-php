<?php
namespace Ignite\Actions;

use Ignite\Action;

abstract class ActionBuffer {

	/**
	 * @var Action[]
	 */
	protected static $actionBuffer = [];

	/**
	 * @return \Ignite\Action[]
	 */
	public static function getBuffer() {
		return self::$actionBuffer;
	}

	public static function clearBuffer() {
		self::$actionBuffer = [];
	}

} 