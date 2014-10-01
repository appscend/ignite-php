<?php

namespace Ignite;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

class ConfigContainer extends Element implements ConfigurationInterface {

	/**
	 * Relative path to the configuration directory
	 */
	const CONFIG_PATH 					= '/src/Ignite/Config';
	/**
	 *	Name of the file where the configuration specification is.
	 */
	const GENERIC_CONFIG_FILE_SPEC 		= 'generic.json';

	/**
	 * @var array The parsed spec file
	 */
	private $configSpec = [];

	/**
	 *	Creates a new configuration container which is used to specify parameters for the current view.
	 */
	public function __construct() {
		parent::__construct('cfg');
		$this->configSpec = json_decode(file_get_contents(LIB_ROOT_DIR.self::CONFIG_PATH.'/'.self::GENERIC_CONFIG_FILE_SPEC), true);
	}

	/**
	 * Appends a configuration specification. Used for more specific views.
	 *
	 * @param string $filepath The file path name relative to the config folder
	 */
	public function appendConfigSpec($filepath) {
		$this->configSpec = array_merge($this->configSpec, json_decode(file_get_contents(LIB_ROOT_DIR.self::CONFIG_PATH.'/'.$filepath), true));
	}

	/**
	 *
	 * Adds properties with prefix
	 *
	 * @param array $props
	 * @param string $prefix
	 * @throws InvalidConfigurationException If properties do not exist in the config spec
	 */
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

		if (!isset($this->prefix_properties[$prefix]))
			$this->prefix_properties[$prefix] = [];

		$this->prefix_properties[$prefix] = array_merge($this->prefix_properties[$prefix], $prefixed);
	}

	/**
	 * @param bool $update
	 * @return array
	 */
	public function render($update = false) {
		$this->properties = $this->translateTags($this->properties);
		$this->properties = (new Processor())->processConfiguration($this, [$this->properties]);

		foreach ($this->prefix_properties as $p)
			$this->properties = array_merge($this->properties, $p);

		return parent::render($update);
	}

	/**
	 *
	 * Translates long names to short names of properties.
	 *
	 * @param array $arr Long name properties
	 * @return array Translated properties
	 * @throws InvalidConfigurationException if the property is invalid
	 */
	private function translateTags(array $arr) {
		$result = [];

		foreach ($arr as $name => $v) {
			if (isset($this->configSpec[$name])) {
				if ($this->configSpec[$name]['type'] == 'enum')
					$result[$this->configSpec[$name]['tag']] = isset($this->configSpec[$name]['enum'][$v]) ? $this->configSpec[$name]['enum'][$v] : $v;
				else
					$result[$this->configSpec[$name]['tag']] = $v;
			}
			else
				throw new InvalidConfigurationException("Option $name is not recognized.");
		}

		return $result;
	}

	/**
	 * @return TreeBuilder
	 */
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
			if ($field['type'] == 'ref') {
				$node = call_user_func_array([$node, $methods[$this->configSpec[$field['ref']]['type']]], [$this->configSpec[$field['ref']]['tag']]);

				if (isset($this->configSpec[$field['ref']]['min']))
					$node = $node->min($this->configSpec[$field['ref']]['min']);
				if (isset($this->configSpec[$field['ref']]['max']))
					$node = $node->max($this->configSpec[$field['ref']]['max']);
				if (isset($this->configSpec[$field['ref']]['enum']))
					$node = $node->values($this->configSpec[$field['ref']]['enum']);

			} else if ($field['type'] == 'Action') {
				$node = $this->getActionTree($node, $field['prefix']);
			} else {
				$node = call_user_func_array([$node, $methods[$field['type']]], [$field['tag']]);

				if (isset($field['min']))
					$node = $node->min($field['min']);
				if (isset($field['max']))
					$node = $node->max($field['max']);
				if (isset($field['enum']))
					$node = $node->values($field['enum']);
			}

			if ($field['type'] === 'boolean')
				$node = $node->values(['yes', 'no']);

			$node = $node->end();
		}

		$node->end()->end();

		return $treeBuilder;
	}

	/**
	 * @param NodeBuilder $n
	 * @param $prefix
	 * @return null|NodeBuilder|\Symfony\Component\Config\Definition\Builder\NodeParentInterface
	 */
	private function getActionTree(NodeBuilder $n, $prefix) {
		$n = $n->scalarNode($prefix.'a')->end();
		$n = $n->scalarNode($prefix.'pr')->end();
		$n = $n->scalarNode($prefix.'l')->end();
		$n = $n->scalarNode($prefix.'lp')->end();
		$n = $n->scalarNode($prefix.'aprod')->end();
		$n = $n->scalarNode($prefix.'dprod')->end();

		$n = $n->scalarNode('prod'.$prefix.'a')->end();
		$n = $n->scalarNode('prod'.$prefix.'pr')->end();
		$n = $n->scalarNode('prod'.$prefix.'l')->end();
		$n = $n->scalarNode('prod'.$prefix.'lp')->end();
		$n = $n->scalarNode('prod'.$prefix.'aprod')->end();
		$n = $n->scalarNode('prod'.$prefix.'dprod')->end();
		$n = $n->scalarNode('prod'.$prefix.'rsk')->end();
		$n = $n->scalarNode('prod'.$prefix.'rsv')->end();
		$n = $n->scalarNode('prod'.$prefix.'conf')->end();
		$n = $n->scalarNode('prod'.$prefix.'del')->end();
		$n = $n->scalarNode('prod'.$prefix.'tavi')->end();

		$n = $n->scalarNode($prefix.'rsk')->end();
		$n = $n->scalarNode($prefix.'rsv')->end();

		$n = $n->scalarNode('rs'.$prefix.'a')->end();
		$n = $n->scalarNode('rs'.$prefix.'pr')->end();
		$n = $n->scalarNode('rs'.$prefix.'l')->end();
		$n = $n->scalarNode('rs'.$prefix.'lp')->end();
		$n = $n->scalarNode('rs'.$prefix.'aprod')->end();
		$n = $n->scalarNode('rs'.$prefix.'dprod')->end();
		$n = $n->scalarNode('rs'.$prefix.'rsk')->end();
		$n = $n->scalarNode('rs'.$prefix.'rsv')->end();
		$n = $n->scalarNode('rs'.$prefix.'conf')->end();
		$n = $n->scalarNode('rs'.$prefix.'del')->end();
		$n = $n->scalarNode('rs'.$prefix.'tavi')->end();

		$n = $n->scalarNode($prefix.'conf')->end();
		$n = $n->scalarNode($prefix.'del')->end();
		//the last element has its end after the switch statement ends in the getConfigTreeBuilder method
		$n = $n->scalarNode($prefix.'tavi');

		return $n;
	}

} 