<?php
namespace Ignite\Modules\Homepage;
use Ignite\Module;
use Ignite\Application;
use Ignite\View;
use Ignite\Action;

class Homepage extends Module
{
    function views(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $controllers->get('hello/{name}', function (Application $app, $name) {
            return "Salut ".$name;
        })->bind("homepage");
        
        $controllers->get('bye', function (Application $app) {
            $view = new View();
            //$view->config->background_color = "A000ff";
           	/*$view->sayHi = function() use ($view) {

            };*/

			$action = new Action();
			$action->name = "alert:s";
			$action->parameters = "Salut<test></test>";
            $view->addElement($action);
            
            return $view;
        })->bind("endpage");

        return $controllers;
    }
}
