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
	const ACTION_GROUP_ELEMENTS_SPEC	= 'action_group_elements.json';

	protected $configFileName 	= null;
	private $configSpec 		= null;
	protected $contents			= [
		'config' => [],
		'elements' => [],
		'actionGroups' => []
	];

	/**
	 * @var Application
	 */
	protected $app;

	protected $viewID;
	
	public function __construct($app, $viewID) {
		parent::__construct('par');
		$this->contents['config'] = new Registry('cfg');
		$this->contents['actionGroups'] = new ViewElementsContainer(self::ACTION_GROUP_ELEMENTS_SPEC, 'ags');
		$this->contents['actionGroups']->_vars[0] = ['ag' => []];
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
	
	protected function addElement(Registry $element) {
		if ($element instanceof Registry) {
			$this->contents['elements'] = $element;
		}
		else
			throw new \InvalidArgumentException('Element must extend Registry');
	}

	public function addAction(Action $action) {

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

	public function addActionGroup(array $actions, $name = null) {
		$idx = count($this->contents['actionGroups']->_vars[0]['ag']);
		$this->contents['actionGroups']->_vars[0]['ag'][$idx] = ['age' => []];

		if (isset($name))
			$this->contents['actionGroups']->_vars[0]['ag'][$idx]['agn'] = $name;

		foreach ($actions as $a) {
			$this->contents['actionGroups']->_vars[0]['ag'][$idx]['age'][] = $a;
		}

		return $idx;
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
