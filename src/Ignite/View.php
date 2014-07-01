<?php

namespace Ignite;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Serializer as Serializer;

abstract class View extends Registry implements ConfigurationInterface {

	const CONFIG_PATH 				= '/src/Ignite/Config';
	const GENERIC_CONFIG_FILE_SPEC 	= 'generic.json';

	protected $configFileName 	= null;
	protected $configSpec 		= null;
	protected $contents			= ['config' => [], 'elements' => []];
	
	function __construct() {
		parent::__construct('par');
		$this->contents['config'] = new Registry('cfg');
		$this->configSpec = json_decode(file_get_contents(ROOT_DIR.self::CONFIG_PATH.'/'.self::GENERIC_CONFIG_FILE_SPEC), true);
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

	public function translateTags(array $array) {
		$translated = [];
		foreach($array as $k => $v) {
			$translated[$k] = $v['tag'];
		}

		return $translated;
	}

	public function getConfigSpec() {
		return $this->configSpec;
	}

	protected function loadSpecFile() {
		if (!is_readable(ROOT_DIR.self::CONFIG_PATH.'/'.$this->configFileName))
			throw new FileNotFoundException("Configuration file '{$this->configFileName}' not found or not readable.");

		$this->configSpec = array_merge($this->configSpec, json_decode(file_get_contents(ROOT_DIR.self::CONFIG_PATH.'/'.$this->configFileName), true));
	}
	
	public function getConfigTreeBuilder() {
		$methods = [
			'string' => 'scalarNode',
			'boolean' => 'enumNode',
			'integer' => 'integerNode',
			'enum' => 'enumNode'
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
