<?php

namespace Ignite\EventListener;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Ignite\View;
use Ignite\Application;
use Ignite\Helpers\XmlDomConstruct;

class ViewToResponseListener implements EventSubscriberInterface
{
	protected $_app;

    public function __construct(Application $app) {
        $this->_app = $app;
    }


    public function onKernelView(GetResponseForControllerResultEvent $event) {
        $response = $event->getControllerResult();
		
        if ($response instanceof View) {
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