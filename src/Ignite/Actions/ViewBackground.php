<?php

namespace Ignite\Actions;

use Ignite\Action;

class ViewBackground {

	public static function store($data, $key, $form) {
		return new Action('s:k:gid:', func_get_args());
	}

	public static function remove($key, $form) {
		return new Action('r:gid:', func_get_args());
	}

	public static function removeAll($form) {
		return new Action('rgid:', func_get_args());
	}

	public static function secureStore($data, $key) {
		return new Action('ss:k:', func_get_args());
	}

	public static function removeSecure($key) {
		return new Action('rs:', func_get_args());
	}

} 