<?php

namespace Ignite\Views;
use Ignite\View;

class CameraView extends View{

	public function __construct($app, $viewID) {
		parent::__construct($app, $viewID);
		$this->contents['config']->appendConfigFile('Camera/config.json');
	}

} 