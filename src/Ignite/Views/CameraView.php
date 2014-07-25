<?php

namespace Ignite\Views;
use Ignite\ConfigContainer;
use Ignite\View;

class CameraView extends View{

	public function __construct($app, $viewID) {
		parent::__construct($app);
		$this->viewID = $viewID;
		$this->config = $this->prependChild(new ConfigContainer());
		$this->config->appendConfigSpec('Camera/config.json');
		$this->config['view_id'] = $viewID;
		$this->config['view_type'] = 'cam';
		$this->config->view = $this;
	}

} 