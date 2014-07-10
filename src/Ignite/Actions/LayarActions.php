<?php

namespace Ignite\Actions;

use Ignite\Action;

class LayarActions {

	public static function start() {
		return new Action('startListening');
	}

} 