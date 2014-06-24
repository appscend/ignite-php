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
	
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $response = $event->getControllerResult();
		
        if ($response instanceof View) {
        	$viewId = $event->getRequest()->get("_route");
        	
        	$configData = $tabletConfigData = $androidConfigData = $androidTabletConfigData = $tallDeviceConfigData = array();
        	try {$configData = $this->_app->scan($viewId.".toml")->validateWith($response);}
			catch(\InvalidArgumentException $ex) {}
			try {$tabletConfigData = $this->_app->scan($viewId."-tablet.toml")->validateWith($response);}
			catch(\InvalidArgumentException $ex) {}
			try {$androidConfigData = $this->_app->scan($viewId."-android.toml")->validateWith($response);}
			catch(\InvalidArgumentException $ex) {}
			try {$androidTabletConfigData = $this->_app->scan($viewId."-android-tablet.toml")->validateWith($response);}
			catch(\InvalidArgumentException $ex) {}
			try {$tallDeviceConfigData = $this->_app->scan($viewId."-tall.toml")->validateWith($response);}
			catch(\InvalidArgumentException $ex) {}
			
			$tabletConfigData = $this->prefixArrayKeys($tabletConfigData, 'pad');
			$androidConfigData = $this->prefixArrayKeys($androidConfigData, 'and');
			$androidTabletConfigData = $this->prefixArrayKeys($androidTabletConfigData, 'andpad');
			$tallDeviceConfigData = $this->prefixArrayKeys($tallDeviceConfigData, 'ff5');
        	
			$objectData = (new Processor())->processConfiguration($response, [$response->config->varsToArray()]);
			
			$finalConfig = array_merge($configData['cfg']?:array(), $tabletConfigData['cfg']?:array(), $androidConfigData['cfg']?:array(), $androidTabletConfigData['cfg']?:array(), $tallDeviceConfigData['cfg']?:array(), $objectData['cfg']);
        	$response->config->setVars($finalConfig);
        	
            $event->setResponse(new Response(var_dump($response->render())));
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::VIEW => array('onKernelView', -10),
        );
    }
}