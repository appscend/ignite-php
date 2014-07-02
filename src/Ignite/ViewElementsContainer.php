<?php

namespace Ignite;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class ViewElementsContainer extends Registry implements ConfigurationInterface{

	private $elemConfigSpec;
	private $translationTags = [];
	private $translatedTags = [];

	public function __construct($fileSpecPath, $wrapper = null) {
		parent::__construct($wrapper);
		$this->elemConfigSpec = json_decode(file_get_contents(ROOT_DIR.'/'.View::CONFIG_PATH.'/'.$fileSpecPath), true);
	}

	private function getTranslation($arr) {
		foreach($arr as $k => $v) {
			if (!isset($v['tag'])) {
				$this->getTranslation($v);
				return ;
			}

			$this->translationTags[$k] = $v['tag'];
		}
	}

	private function translateTags(array $arr, array &$where = null) {
		foreach ($arr as $k => $v) {
			$key = isset($this->translationTags[$k]) ? $this->translationTags[$k] : $k;

			if ($where !== null)
				$where[$key] = $v;

			if (isset($where[$k]) && isset($this->translationTags[$k]))
				unset($where[$k]);

			if (is_array($v) && !isset($v['tag']))
				$this->translateTags($v, $where[$key]);
		}

	}

	private function configTreeArray($node, $arr, $fieldName) {
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
				}
			}

			$node = $node->end();
		}

		return $node->end()->end()->end();
	}

	public function render() {
		if ($this->wrapperTag) {
			$this->getTranslation($this->elemConfigSpec[$this->wrapperTag]);
		}
		else {
			$this->getTranslation($this->elemConfigSpec);
		}

		$this->translateTags($this->_vars, $this->translatedTags);
		$this->_vars = $this->translatedTags;

		return parent::render();
	}

	public function getConfigTreeBuilder() {

		$treeBuilder = new TreeBuilder();

		$node = $treeBuilder->root(0)
			->children();

		foreach ($this->elemConfigSpec as $fieldName => $field) {
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
				}

				$node = $node->end();
			}

		}

		$node->end();

		return $treeBuilder;
	}

} 