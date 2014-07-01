<?php
namespace Ignite\Modules\Homepage;
use Ignite\Module;
use Ignite\Application;
use Ignite\Views;
use Ignite\Action;

class Homepage extends Module {

	public function views(Application $app) {
        $controllers = $app['controllers_factory'];

        $controllers->get('hello/{name}', function (Application $app, $name) {
            return "Salut ".$name;
        })->bind("homepage");
        
        $controllers->get('bye', function (Application $app) {
            $view = new Views\TabBarView($app, "endpage");

			$view->addTab([
				"title" => "Gogoși cu zmoală",
				"image" => "img.png",
				"target_view_type" => "t",
				"post_data" => "test data"
			]);

			$view->addTab([
				"title" => "Gogoși cu zmoală",
				"image" => "img.png",
				"target_view_type" => "t",
				"post_data" => "test data"
			]);
            
            return $view;
        })->bind("endpage");

        return $controllers;
    }
}
