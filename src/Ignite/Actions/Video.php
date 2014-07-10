<?php

namespace Ignite\Actions;

use Ignite\Action;

class Video {

	public static function playModal($video) {
		return new Action('pv:', func_get_args());
	}


} 