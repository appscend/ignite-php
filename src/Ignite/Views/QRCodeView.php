<?php

namespace Ignite\Views;
use Ignite\View;

class QRCodeView extends View{

	public function __construct($app, $viewID) {
		parent::__construct($app, $viewID);
		$this->configFileName = 'QRCode/config.json';
		$this->loadSpecFile();
	}

} 