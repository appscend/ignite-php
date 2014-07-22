<?php

namespace Ignite;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class ConfigContainer extends Element implements ConfigurationInterface {

	const CONFIG_PATH 					= '/src/Ignite/Config';
	const GENERIC_CONFIG_FILE_SPEC 		= 'generic.json';

	private $configSpec = [];

	public function __construct() {
		parent::__construct('cfg');
		$this->configSpec = json_decode(file_get_contents('/home/razvan/proiecte/ignitephp'.self::CONFIG_PATH.'/'.self::GENERIC_CONFIG_FILE_SPEC), true);
	}

	public function appendConfigSpec($filepath) {
		$this->configSpec = array_merge($this->configSpec, json_decode(file_get_contents('/home/razvan/proiecte/ignitephp'.self::CONFIG_PATH.'/'.$filepath), true));
	}

	public function render($update = false) {
		$this->translateTags();

		return parent::render($update);
	}

	private function translateTags() {
		$result = [];

		foreach ($this->properties as $name => $v) {
			if ($this->configSpec[$name])
				$result[$this->configSpec[$name]['tag']] = $v;
			else
				$result[$name] = $v;
		}

		$this->properties = $result;
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