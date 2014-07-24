<?php

namespace Ignite;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;

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

	public function addPrefixedProperties(array $props, $prefix) {
		$props = $this->translateTags($props);

		try {
			(new Processor())->processConfiguration($this, [$props]);
		} catch (InvalidConfigurationException $e) {
			throw new InvalidConfigurationException($e->getMessage()." (for prefix '$prefix')");
		}

		$prefixed = [];

		foreach ($props as $k => $v)
			$prefixed[$prefix.$k] = $v;

		$this->prefix_properties[$prefix] = $prefixed;
	}

	public function render($update = false) {
		$this->properties = $this->translateTags($this->properties);
		$this->properties = (new Processor())->processConfiguration($this, [$this->properties]);

		foreach ($this->prefix_properties as $p)
			$this->properties = array_merge($this->properties, $p);

		return parent::render($update);
	}

	private function translateTags(array $arr) {
		$this->isTranslated = true;
		$result = [];

		foreach ($arr as $name => $v) {
			if (isset($this->configSpec[$name]))
				$result[$this->configSpec[$name]['tag']] = $v;
			else
				throw new InvalidConfigurationException("Option $name is not recognized.");
		}

		return $result;
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

		$node = $root->children();

		foreach ($this->configSpec as $fieldName => $field) {
			if ($field['type'] !== 'ref') {
				$node = call_user_func_array([$node, $methods[$field['type']]], [$field['tag']]);

				if (isset($field['min']))
					$node = $node->min($field['min']);
				if (isset($field['max']))
					$node = $node->max($field['max']);
				if (isset($field['enum']))
					$node = $node->values($field['enum']);

			} else {
				$node = call_user_func_array([$node, $methods[$this->configSpec[$field['ref']]['type']]], [$this->configSpec[$field['ref']]['tag']]);

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