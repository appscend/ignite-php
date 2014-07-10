<?php

namespace Ignite\Actions;

use Ignite\Action;

class QRCodeActions {

	public static function start() {
		return new Action('startListening');
	}

} 