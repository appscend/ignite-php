<?php
namespace Ignite;

use Ignite\Providers\Logger;
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
			if (isset($this->translationTags[$k])) {
				if ($this->configSpec[$k]['type'] == 'enum') {
					$result[$this->translationTags[$k]] = isset($this->configSpec[$k]['enum'][$v]) ? $this->configSpec[$k]['enum'][$v] : $v;
				} else
					$result[$this->translationTags[$k]] = $v;
			} else
				$result[$k] = $v;
		}

		foreach ($child->getPrefixedProperties() as $key => &$for) {
			if (empty($for)) continue;

			$for = $this->view->processor->processConfiguration($this, [$for]);

			foreach($for as $k => $v) {
				if (isset($this->translationTags[$k])) {
					if ($this->configSpec[$k]['type'] == 'enum') {
						$result[Element::$prefixes[$key].$this->translationTags[$k]] = isset($this->configSpec[$k]['enum'][$v]) ? $this->configSpec[$k]['enum'][$v] : $v;
					} else
						$result[Element::$prefixes[$key].$this->translationTags[$k]] = $v;
				} else
					$result[$k] = $v;
			}
		}

		$child->setProperties($result);

		/**
		 * @var Registry $c
		 */
		foreach ($child as $c)
			$this->translateTags($c);
	}

	public function appendChild(Registry $el) {
		try {
			$result = $this->view->processor->processConfiguration($this, [$el->getProperties()]);
			$el->setProperties($result);

			return parent::appendChild($el);
		} catch (InvalidConfigurationException $e) {
			$this->view->getApp()['ignite_logger']->log($e->getMessage(), Logger::LOG_ERROR);

			return false;
		}
	}

	public function prependChild(Registry $el) {
		try {
			$result = $this->view->processor->processConfiguration($this, [$el->getProperties()]);
			$el->setProperties($result);

			return parent::prependChild($el);
		} catch (InvalidConfigurationException $e) {
			$this->view->getApp()['logger']->log($e->getMessage(), Logger::LOG_ERROR);

			return false;
		}
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
					$node = $node->values(array_keys($field['enum']));

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