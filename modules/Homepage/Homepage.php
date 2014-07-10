<?php
namespace Ignite\Modules\Homepage;
use Ignite\Module;
use Ignite\Application;
use Ignite\Views;

class Homepage extends Module {

	public function views(Application $app) {
        $controllers = $app['controllers_factory'];

        $controllers->get('hello/{name}', function (Application $app, $name) {
            return "Salut ".$name;
        })->bind("homepage");
        
        $controllers->get('bye', function (Application $app) {
            $view = new Views\ListView($app, "endpage");

			$view->addSection(["name" => "Test section", "code" => "xxx"]);
			$view->addListElement([
				"subtext" => "asdsjadhsjkdh kashd kajsh dkajshd kjashd kjas dh",
				"description" => " 239487 329023y4i2h342hg34"
			], 0);

			/*$view->addMenu([
				"title" => "Gogoși cu zmoală",
				"attach_location" => "yes",
				"target_view_type" => "t",
				"post_data" => "test data"
			]);

			$view->addMenu([
				"title" => "Gogoși cu zmoală",
				"attach_location" => "yes",
				"target_view_type" => "t",
				"post_data" => "test data"
			]);*/
            
            return $view;
        })->bind("endpage");

        return $controllers;
    }
}
