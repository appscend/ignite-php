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
	
	public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root(0);
        
        $node = $rootNode->children()->arrayNode('cfg')->ignoreExtraKeys();
        foreach ($this->configSpec as $fieldName => $fieldData)
        	$node->fixXmlConfig($fieldName, $fieldData['tag']);
        $node = $node->children();
        foreach ($this->configSpec as $fieldName => $fieldData) {
        	$node->scalarNode($fieldData['tag'])->beforeNormalization()->ifArray()->then(function($v) {return $v[0];})->end()->end();
        }
        $node->end()->end()->end();
        	
        return $treeBuilder;
    }
    
}

?>