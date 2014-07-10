<?php

namespace Ignite\Actions;

use Ignite\Action;

class ListActions {

	public static function toggleSelectable() {
		return new Action('tsel');
	}

	public static function setSelectable($state) {
		return new Action('mpraa:', func_get_args());
	}

	public static function toggleSelectUnique() {
		return new Action('tselu');
	}

	public static function executeActionsSelected() {
		return new Action('execsel');
	}

	public static function executeActionsSelectedAndToggle() {
		return new Action('execasel');
	}
} 