<?php

namespace Ignite;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Serializer as Serializer;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

abstract class View extends Registry implements ConfigurationInterface {

	const CONFIG_PATH 					= '/src/Ignite/Config';
	const GENERIC_CONFIG_FILE_SPEC 		= 'generic.json';
	const ACTION_GROUP_SPEC				= 'action_group_elements.json';
	const LAUNCH_ACTIONS_SPEC			= 'launch_actions.json';
	const BUTTON_ELEMENTS_SPEC			= 'button_elements.json';
	const MENU_ELEMENTS_SPEC			= 'menu_elements.json';

	protected $configFileName 	= null;
	private $configSpec 		= null;
	protected $contents			= [
		'config' 				=> null,
		'elements' 				=> null,
		'actionGroups' 			=> null,
		'launchActions' 		=> null,
		'visibleLaunchActions' 	=> null,
		'hiddenLaunchActions'	=> null,
		'buttons' 				=> null,
		'menus'					=> null
	];

	/**
	 * @var Application
	 */
	protected $app;

	protected $viewID;
	
	public function __construct($app, $viewID) {
		parent::__construct('par');
		$this->contents['config'] 				= new Registry('cfg');

		$this->contents['actionGroups'] 		= new ViewElementsContainer(self::ACTION_GROUP_SPEC, 'ags');

		$this->contents['buttons'] 				= new ViewElementsContainer(self::BUTTON_ELEMENTS_SPEC, 'bs');

		$this->contents['launchActions'] 		= new ViewElementsContainer(self::LAUNCH_ACTIONS_SPEC, 'las');
		$this->contents['visibleLaunchActions'] = new ViewElementsContainer(self::LAUNCH_ACTIONS_SPEC, 'vas');
		$this->contents['hiddenLaunchActions'] 	= new ViewElementsContainer(self::LAUNCH_ACTIONS_SPEC, 'has');

		$this->contents['menus'] 				= new ViewElementsContainer(self::MENU_ELEMENTS_SPEC, 'ms');

		$this->configSpec = json_decode(file_get_contents(ROOT_DIR.self::CONFIG_PATH.'/'.self::GENERIC_CONFIG_FILE_SPEC), true);
		$this->app = $app;
		$this->viewID = $viewID;
	}
	
	public function __get($name) {
		switch ($name) {
			case "elements": return $this->contents['elements'];
			case "config": return $this->contents['config'];
		}
	}
	
	protected function addElementContainer(ViewElementsContainer $element) {
		if ($element instanceof ViewElementsContainer) {
			$this->contents['elements'] = $element;
		}
		else
			throw new \InvalidArgumentException('Element must be instance of \\Ignite\\ViewElementsContainer');
	}

	public function addLaunchAction(Action $action, $type = null) {
		switch($type) {
			case Action::LAUNCH_ACTION_VISIBLE: {
				$wrapperTag = 'va';
				$where = 'visibleLaunchActions';

				break;
			}
			case Action::LAUNCH_ACTION_HIDDEN: {
				$wrapperTag = 'ha';
				$where = 'hiddenLaunchActions';

				break;
			}
			default: {
				$wrapperTag = 'la';
				$where = 'launchActions';
			}
		}

		if (!isset($this->contents[$where]->_vars[$wrapperTag])) {
			$this->contents[$where]->_vars[$wrapperTag] = [];
		}

		$this->contents[$where]->_vars[$wrapperTag][] = $action;
	}

	public function addMenu(Element $menu = null) {
		if ($menu == null)
			$menu = new Element();

		$menu->wrapperTag = 'm';

		$this->contents['menus']->_vars[] = $menu;

		return $menu;
	}

	/**
	 * @param array|Element|null $element
	 * @param Element $menu
	 * @return Element
	 */
	public function addMenuElement($element = null, Element $menu) {
		if ($element == null)
			$element = new Element();
		else if (is_array($element))
			$element = new Element($element);

		$element->wrapperTag = 'me';
		$menu->addChild($element);

		return $element;
	}


	/**
	 * @param Action[] $actions
	 * @param null|string $name
	 * @return int
	 */
	public function addActionGroup(array $actions, $name = null) {
		$actionGroup = new Element([], 'ag');

		$this->contents['actionGroups']->_vars[] = $actionGroup;

		$idx = count($this->contents['actionGroups']->_vars);
		if (isset($name))
			$actionGroup->_vars['agn'] = $name;

		foreach ($actions as $a) {
			$a->wrapperTag = 'age';

			$actionGroup->addChild($a);
		}

		return $idx;
	}

	/**
	 * @param array|Element|null $group
	 * @return Element
	 */
	public function addButtonGroup($group = null) {
		if ($group == null)
			$group = new Element([], 'bg');
		else if (!$group instanceof Element)
			$group = new Element($group, 'bg');

		$group->wrapperTag = 'bg';

		$this->contents['buttons']->_vars[] = $group;

		return $group;
	}

	/**
	 * @param array|Element $button
	 * @param Element|null $group
	 * @return Element
	 */
	public function addButtonElement($button, $group = null) {
		if (!$button instanceof Element)
			$button = new Element($button);

		if ($group !== null) {
			$button->wrapperTag = 'b';
			$group->addChild($button);
		} else {

			if (!isset($this->contents['buttons']->_vars['b']))
				$this->contents['buttons']->_vars['b'] = [];

			$this->contents['buttons']->_vars['b'][] = $button;
		}

		return $button;
	}

	protected function loadSpecFile() {
		if (!is_readable(ROOT_DIR.self::CONFIG_PATH.'/'.$this->configFileName))
			throw new FileNotFoundException("Configuration file '{$this->configFileName}' not found or not readable.");

		$this->configSpec = array_merge($this->configSpec, json_decode(file_get_contents(ROOT_DIR.self::CONFIG_PATH.'/'.$this->configFileName), true));
	}

	private function prefixArrayKeys($array, $prefix) {
		$result = array();
		foreach ($array as $area=>$configs) {
			array_walk($configs, function ($value,$key) use (&$result, $prefix, $area) {
				$result[$area][$prefix.$key] = $value;
			});
		}

		return $result;
	}

	private function translateTags(array $array) {
		$translated = [];
		foreach($array as $k => $v) {
			$translated[$k] = $v['tag'];
		}

		return $translated;
	}

	private function translateConfigTags($translate, $config) {
		$translation = ['cfg' => []];

		foreach($config['cfg'] as $k => $v) {
			$translation['cfg'][$translate[$k]] = $v;
		}

		return $translation;
	}

	public function render() {
		$translatedTags = $this->translateTags($this->configSpec);

		$configData = $tabletConfigData = $androidConfigData = $androidTabletConfigData = $tallDeviceConfigData = [];
		try {$configData 				= $this->translateConfigTags($translatedTags, $this->app->scan($this->viewID.".toml")->validateWith($this));}
		catch(\InvalidArgumentException $ex) {}
		try {$tabletConfigData 			= $this->translateConfigTags($translatedTags, $this->app->scan($this->viewID."-tablet.toml")->validateWith($this));}
		catch(\InvalidArgumentException $ex) {}
		try {$androidConfigData 		= $this->translateConfigTags($translatedTags, $this->app->scan($this->viewID."-android.toml")->validateWith($this));}
		catch(\InvalidArgumentException $ex) {}
		try {$androidTabletConfigData 	= $this->translateConfigTags($translatedTags, $this->app->scan($this->viewID."-android-tablet.toml")->validateWith($this));}
		catch(\InvalidArgumentException $ex) {}
		try {$tallDeviceConfigData 		= $this->translateConfigTags($translatedTags, $this->app->scan($this->viewID."-tall.toml")->validateWith($this));}
		catch(\InvalidArgumentException $ex) {}

		$tabletConfigData 			= $this->prefixArrayKeys($tabletConfigData, 'pad');
		$androidConfigData 			= $this->prefixArrayKeys($androidConfigData, 'and');
		$androidTabletConfigData 	= $this->prefixArrayKeys($androidTabletConfigData, 'andpad');
		$tallDeviceConfigData 		= $this->prefixArrayKeys($tallDeviceConfigData, 'ff5');

		$objectData = (new Processor())->processConfiguration($this, [$this->config->getVars()]);
		$finalConfig = array_merge(isset($configData['cfg'])?$configData['cfg']:[], isset($tabletConfigData['cfg'])?$tabletConfigData['cfg']:[], isset($androidConfigData['cfg'])?$androidConfigData['cfg']:[], isset($androidTabletConfigData['cfg'])?$androidTabletConfigData['cfg']:[], isset($tallDeviceConfigData['cfg'])?$tallDeviceConfigData['cfg']:[], $objectData['cfg']);

		/*(new Processor())->processConfiguration($this->elements, [$this->elements->render()]);
		(new Processor())->processConfiguration($this->actionGroups, [$this->actionGroups->render()]);*/

		if (!isset($finalConfig['vt']))
			throw new InvalidConfigurationException("'view_type' variable is not configured.");

		//$this->elements->setVars($this->translateTags());
		$this->config->setVars($finalConfig);

		return parent::render();
	}

	public function getConfigTreeBuilder() {
		$methods = [
			'string' => 'scalarNode',
			'boolean' => 'enumNode',
			'integer' => 'integerNode',
			'enum' => 'enumNode',
			'float' => 'floatNode'
		];

		$treeBuilder = new TreeBuilder();
		$root = $treeBuilder->root(0);

		$cfgNode = $root->children()->arrayNode('cfg')->ignoreExtraKeys()->isRequired();
		$node = $cfgNode->children();

		foreach ($this->configSpec as $fieldName => $field) {
			if ($field['type'] !== 'ref') {
				$node = call_user_func_array([$node, $methods[$field['type']]], [$fieldName]);

				if (isset($field['min']))
					$node = $node->min($field['min']);
				if (isset($field['max']))
					$node = $node->max($field['max']);
				if (isset($field['enum']))
					$node = $node->values($field['enum']);

			} else {
				$node = call_user_func_array([$node, $methods[$this->configSpec[$field['ref']]['type']]], [$field['ref']]);

				if (isset($this->configSpec[$field['ref']]['min']))
					$node = $node->min($this->configSpec[$field['ref']]['min']);
				if (isset($this->configSpec[$field['ref']]['max']))
					$node = $node->max($this->configSpec[$field['ref']]['max']);
				if (isset($this->configSpec[$field['ref']]['enum']))
					$node = $node->values($this->configSpec[$field['ref']]['enum']);
			}

			if ($field['type'] === 'boolean')
				$node = $node->values(['yes', 'no']);

			$node = $node->end();
		}

		$node->end()->end();

        return $treeBuilder;
    }
    
}
