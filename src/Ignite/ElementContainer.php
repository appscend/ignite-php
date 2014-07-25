<?php
namespace Ignite;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

class ElementContainer extends Element implements ConfigurationInterface{

	private $configSpec = [];
	private $translationTags = [];
	private $isTranslated = false;

	public function __construct($specFile, $tag = null) {
		parent::__construct($tag);
		$this->configSpec = json_decode(file_get_contents(ROOT_DIR.ConfigContainer::CONFIG_PATH.'/'.$specFile), true);
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
		$this->isTranslated = true;
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

	public function appendChild(Registry $el) {
		$el->setProperties($this->view->processor->processConfiguration($this, [$el->getProperties()]));

		return parent::appendChild($el);
	}

	public function prependChild(Registry $el) {
		$el->setProperties($this->view->processor->processConfiguration($this, [$el->getProperties()]));

		return parent::prependChild($el);
	}

	public function render($update = false) {
		if (!$this->isTranslated) {
			$this->getTranslation($this->configSpec);
			$this->translateTags($this);
		}

		return parent::render($update);
	}

	public function getConfigTreeBuilder() {
		$treeBuilder = new TreeBuilder();

		$node = $treeBuilder->root(0)
			->children();

		foreach ($this->configSpec as $fieldName => $field) {
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

				case 'Action': {
					$node = $this->getActionTree($node, $field['prefix']);

					break;
				}

				default: {
				throw new InvalidConfigurationException("Type '{$field['type']}' does not exist.");
				}
			}

			$node = $node->end();
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

					case 'Action': {
						$node = $this->getActionTree($node, $fn['prefix']);

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
		//the last element has its end after the switch statement ends
		$n = $n->scalarNode($prefix.'tavi');

		return $n;
	}

}