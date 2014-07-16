<?php
namespace Ignite\Modules\Homepage;
use Ignite\Action;
use Ignite\Actions\CoverFlowActions;
use Ignite\Actions\ListActions;
use Ignite\Actions\Navigation;
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
            $view = new Views\CoverflowView($app, "endpage");

			$view->addImage(["image" => "Test section", "name" => "xxx"]);
			$view->addImage([
				"image" => "asdsjadhsjkdh kashd kajsh dkajshd kjashd kjas dh",
				"name" => " 239487 329023y4i2h342hg34"
			], 0);

			$view->getImage(0)->special = function() {return Navigation::refresh();};
			$view->getImage(1)->doStuff = function() {
				return [ListActions::executeActionsSelected()->requiresLogin('fb'),
						ListActions::toggleSelectable(),
						ListActions::setSelectable(1)->on("test")
				];
			};

			$view->addLaunchAction(Navigation::refresh());
			$view->addLaunchAction(CoverFlowActions::scrollTo(10,10), Action::LAUNCH_ACTION_VISIBLE);
			$view->addLaunchAction(CoverFlowActions::flip(), Action::LAUNCH_ACTION_VISIBLE);

			$menu = $view->addMenu();

			$view->addMenuElement(["text" => "asdadad"], $menu);
			$view->addMenuElement(["text" => "text 2"], $menu);

			$view->addButtonElement(["text" => "buton fara grup"]);
			$view->addButtonElement(["text" => "buton in grup"], $view->addButtonGroup(["position" => "rn"]));
            
            return $view;
        })->bind("endpage");

        return $controllers;
    }
}
