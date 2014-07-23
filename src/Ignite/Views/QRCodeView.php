<?php

namespace Ignite\Views;
use Ignite\View;
use Ignite\ConfigContainer;

class QRCodeView extends View{

	public function __construct($app, $viewID) {
		parent::__construct($app);
		$this->viewID = $viewID;
		$this->config = $this->prependChild(new ConfigContainer());
		$this->config->appendConfigSpec('QRCode/config.json');
		$this->config['view_id'] = $viewID;
	}

} 