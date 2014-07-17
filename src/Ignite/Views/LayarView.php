<?php

namespace Ignite\Views;
use Ignite\View;

class LayarView extends View{

	public function __construct($app, $viewID) {
		parent::__construct($app, $viewID);
		$this->contents['config']->appendConfigFile('Layar/config.json');
	}

} 