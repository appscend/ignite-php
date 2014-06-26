<?php

namespace Ignite;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Serializer as Serializer;

class View extends Registry implements ConfigurationInterface {

	const CONFIG_PATH 				= '/src/Ignite/Config';
	const GENERIC_CONFIG_FILE_SPEC 	= 'generic.json';

	public $config;
	public $elementRepresentation;
	
	protected $addons;
	protected $configFileName 	= null;
	protected $configSpec 		= null;
	
	function __construct() {
		parent::__construct('par');
		$this->config = new Registry('cfg');
		$this->elementRepresentation = new Registry('ef');
		$this->addons = array(
			'es' => array(),
			'bs' => array(),
			'mes' => array(),
			'ets' => array()
		);
		$this->actionContainer = array();

		$this->configSpec = json_decode(file_get_contents(ROOT_DIR.self::CONFIG_PATH.'/'.self::GENERIC_CONFIG_FILE_SPEC), true);

	}
	
	public function __get($name) {
		switch ($name) {
			case "elements": return $this->addons['es'];
			case "buttons": return $this->addons['bs'];
			case "menus": return $this->addons['mes'];
			case "templates": return $this->addons['ets'];
		}
	}
	
	public function addElement($element) {
		if ($element instanceof Registry) {
			$element->actionContainer = $this->actionContainer;
			$this->addons['es'][] = $element;
		}
		else
			throw new \InvalidArgumentException('Element must extend Registry');
	}

	public function translateTags() {
		$translated = [];
		foreach($this->configSpec as $k => $v) {
			$translated[$k] = $v['tag'];
		}

		return $translated;
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
