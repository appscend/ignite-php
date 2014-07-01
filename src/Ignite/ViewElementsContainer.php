<?php

namespace Ignite;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class ViewElementsContainer extends Registry implements ConfigurationInterface{

	private $elemConfigSpec;

	public function __construct($fileSpecPath) {
		parent::__construct();
		$this->elemConfigSpec = json_decode(file_get_contents(ROOT_DIR.'/'.View::CONFIG_PATH.'/'.$fileSpecPath), true);
	}

	private function configTreeArray($node, $arr, $fieldName) {
		$node = $node->arrayNode($fieldName)->prototype('array')->children();
		foreach ($arr as $fn => $f) {
			//this array contains elements which are arrays, otherwise we should always have a tag property present
			if (!isset($f['tag']))
				$node = $this->configTreeArray($node, $f, $fn);

			switch ($f['type']) {
				case 'string': {
					$node = $node->scalarNode($fn);

					break;
				}

				case 'integer': {
					$node = $node->integerNode($fn);

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
					$node = $node->values(['true', 'false']);

					break;
				}
			}

			$node = $node->end();
		}

		return $node->end()->end()->end();
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

					case 'integer': {
						$node = $node->integerNode($fieldName);

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