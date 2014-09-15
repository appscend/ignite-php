<?php

namespace Ignite\Views;
use Ignite\ConfigContainer;
use Ignite\View;

class LayarView extends View{

	const ACTIONS_CONFIG_SPEC_FILE = 'Layar/actions.json';

	public function __construct($app, $viewID) {
		parent::__construct($app, $viewID);

		$this->config = $this->prependChild(new ConfigContainer());
		$this->config->appendConfigSpec('Layar/config.json');
		$this->config['view_id'] = $viewID;
		$this->config['view_type'] = 'lr';
		$this->config->view = $this;

		$this->actionsSpec = array_merge($this->actionsSpec, json_decode(file_get_contents(LIB_ROOT_DIR.ConfigContainer::CONFIG_PATH.'/'.self::ACTIONS_CONFIG_SPEC_FILE), true));
		$this->parseConfiguration();
	}

} 