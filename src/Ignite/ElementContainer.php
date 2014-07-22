<?php
namespace Ignite;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

class ElementContainer extends Element implements ConfigurationInterface{

	private $configSpec = [];
	private $translationTags = [];

	public function __construct($specFile, $tag = null) {
		parent::__construct($tag);
		$this->configSpec = json_decode(file_get_contents('/home/razvan/proiecte/ignitephp'.ConfigContainer::CONFIG_PATH.'/'.$specFile), true);
	}

	private function getTranslation(array $arr) {
		foreach($arr as $k => $v) {
			if (!isset($v['tag'])) {
				$this->getTranslation($v);
				return ;
			}

			$this->translationTags[$k] = $v['tag'];
		}
	}

	private function translateTags(Registry $child) {
		$result = [];

		foreach ($child->getProperties() as $k => $v) {
			if (isset($this->translationTags[$k]))
				$result[$this->translationTags[$k]] = $v;
			else
				$result[$k] = $v;
		}

		$child->setProperties($result);

		foreach ($child->getChildren() as $c)
			$this->translateTags($c);
	}

	public function render($update = false) {
		$this->getTranslation($this->configSpec);
		$this->translateTags($this);

		return parent::render($update);
	}

	public function getConfigTreeBuilder() {
		$treeBuilder = new TreeBuilder();

		$node = $treeBuilder->root(0)
			->children();

		foreach ($this->$configSpec as $fieldName => $field) {
			if (is_array($field)) {
				$node = $this->configTreeArray($node, $field, $fieldName);
			} else {
				switch ($field['type']) {
					case 'string': {
						$node = $node->scalarNode($fieldName);

						break;
					}

					case 'float':
					case 'integer': {
						$node = $node->node($fieldName, $field['type']);

						if (isset($field['min']))
							$node = $node->min($field['min']);

						if (isset($field['max']))
							$node = $node->max($field['max']);

						break;
					}

					case 'enum': {
						$node = $node->enumNode($fieldName);
						$node = $node->values($field['enum']);

						break;
					}

					case 'boolean': {
						$node = $node->enumNode($fieldName);
						$node = $node->values(['true', 'false']);

						break;
					}

					default: {
					throw new InvalidConfigurationException("Type '{$field['type']}' does not exist.");
					}
				}

				$node = $node->end();
			}

		}

		$node->end();

		return $treeBuilder;
	}

	private function configTreeArray(NodeBuilder $node, $arr, $fieldName) {
		$node = $node->arrayNode($fieldName)->prototype('array')->children();
		foreach ($arr as $fn => $f) {
			//this array contains elements which are arrays, otherwise we should always have a tag property present
			if (!isset($f['tag'])) {
				$node = $this->configTreeArray($node, $f, $fn);
			} else {
				switch ($f['type']) {
					case 'string': {
						$node = $node->scalarNode($fn);

						break;
					}

					case 'float':
					case 'integer': {
						$node = $node->node($fn, $f['type']);

						if (isset($f['min']))
							$node = $node->min($f['min']);

						if (isset($f['max']))
							$node = $node->max($f['max']);

						break;
					}

					case 'enum': {
						$node = $node->enumNode($fn);
						$node = $node->values($f['enum']);

						break;
					}

					case 'boolean': {
						$node = $node->enumNode($fn);
						$node = $node->values(['yes', 'no']);

						break;
					}

					default: {
					throw new InvalidConfigurationException("Type '{$f['type']}' does not exist.");
					}
				}

				$node = $node->end();
			}
		}

		return $node->end()->end()->end();
	}

}