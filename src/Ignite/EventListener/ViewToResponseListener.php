<?php

namespace Ignite\EventListener;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Config\Definition\Processor;
use Ignite\View;
use Ignite\Application;
use Ignite\Registry;
use Ignite\Action;
use Ignite\Helpers\XmlDomConstruct;

class ViewToResponseListener implements EventSubscriberInterface
{
	protected $_app;

    public function __construct(Application $app)
    {
        $this->_app = $app;
    }
    
    private function prefixArrayKeys($array, $prefix) {
	    $result = array();
	    foreach ($array as $area=>$configs) {
			array_walk($configs, function ($value,$key) use (&$result, $prefix, $area) {
			    $result[$area][$prefix.$key] = $value;
			});
		}
		
		return $result;
    }

	private function translateConfigTags($translate, $config) {
		$translation = ['cfg' => []];

		foreach($config['cfg'] as $k => $v) {
			$translation['cfg'][$translate[$k]] = $v;
		}

		return $translation;
	}

    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $response = $event->getControllerResult();
		
        if ($response instanceof View) {
        	$viewId = $event->getRequest()->get("_route");
			$translatedTags = $response->translateTags();

        	$configData = $tabletConfigData = $androidConfigData = $androidTabletConfigData = $tallDeviceConfigData = [];
        	try {$configData 				= $this->translateConfigTags($translatedTags, $this->_app->scan($viewId.".toml")->validateWith($response));}
			catch(\InvalidArgumentException $ex) {}
			try {$tabletConfigData 			= $this->translateConfigTags($translatedTags, $this->_app->scan($viewId."-tablet.toml")->validateWith($response));}
			catch(\InvalidArgumentException $ex) {}
			try {$androidConfigData 		= $this->translateConfigTags($translatedTags, $this->_app->scan($viewId."-android.toml")->validateWith($response));}
			catch(\InvalidArgumentException $ex) {}
			try {$androidTabletConfigData 	= $this->translateConfigTags($translatedTags, $this->_app->scan($viewId."-android-tablet.toml")->validateWith($response));}
			catch(\InvalidArgumentException $ex) {}
			try {$tallDeviceConfigData 		= $this->translateConfigTags($translatedTags, $this->_app->scan($viewId."-tall.toml")->validateWith($response));}
			catch(\InvalidArgumentException $ex) {}

			$tabletConfigData 			= $this->prefixArrayKeys($tabletConfigData, 'pad');
			$androidConfigData 			= $this->prefixArrayKeys($androidConfigData, 'and');
			$androidTabletConfigData 	= $this->prefixArrayKeys($androidTabletConfigData, 'andpad');
			$tallDeviceConfigData 		= $this->prefixArrayKeys($tallDeviceConfigData, 'ff5');

			$objectData = (new Processor())->processConfiguration($response, [$response->config->varsToArray()]);
			
			$finalConfig = array_merge(isset($configData['cfg'])?$configData['cfg']:[], isset($tabletConfigData['cfg'])?$tabletConfigData['cfg']:[], isset($androidConfigData['cfg'])?$androidConfigData['cfg']:[], isset($androidTabletConfigData['cfg'])?$androidTabletConfigData['cfg']:[], isset($tallDeviceConfigData['cfg'])?$tallDeviceConfigData['cfg']:[], $objectData['cfg']);
        	$response->config->setVars($finalConfig);

			$xmlConstruct = new XmlDomConstruct('1.0', 'UTF-8');
			$xmlConstruct->fromMixed($response->render());
			$responseContent = $xmlConstruct->saveXML(null, LIBXML_NOEMPTYTAG);

            $event->setResponse(new Response($responseContent, Response::HTTP_OK, ['Content-type' => 'text/xml']));
        }
    }

    public static function getSubscribedEvents() {
        return array(
            KernelEvents::VIEW => array('onKernelView', -10),
        );
    }
}