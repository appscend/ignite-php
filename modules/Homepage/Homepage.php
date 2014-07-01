<?php
namespace Ignite\Modules\Homepage;
use Ignite\Module;
use Ignite\Application;
use Ignite\Views;
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
            $view = new Views\TabBarView();

			$view->addTab([
				"title" => "Gogoși cu zmoală",
				"image" => "img.png",
				"target_view_type" => "t",
				"data" => "test data"
			]);

			$view->addTab([
				"title" => "Gogoși cu zmoală",
				"image" => "img.png",
				"target_view_type" => "t",
				"data" => "test data"
			]);

            //$view->config->background_color = "A000ff";
           	/*$view->sayHi = function() use ($view) {

            };*/

			/*$action = new Action();
			$action->name = "alert:s";
			$action->parameters = "Salut<test></test>";
            $view->addElement($action);*/
            
            return $view;
        })->bind("endpage");

        return $controllers;
    }
}
