<?php
namespace Ignite\Modules\Homepage;
use Ignite\Action;
use Ignite\Actions;
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
			$view->parseConfiguration(MODULES_DIR.'/Homepage/config/endpage.toml');

			$view->addImage(["image" => "Test section", "name" => "xxx"]);
			$view->addImage([
				"image" => "asdsjadhsjkdh kashd kajsh dkajshd kjashd kjas dh",
				"name" => " 239487 329023y4i2h342hg34"
			], 0);

			$view->getImage(0)->onTap(function() {return Actions\Navigation::refresh();});
			$view->getImage(1)->onTap(function() {
				return [Actions\CoverFlowActions::startSlideshow()->requiresLogin('fb'),
					Actions\CoverFlowActions::flip(),
					Actions\System::alert("bla") ->on("test")
				];
			});

			$view->addLaunchAction(Actions\Navigation::refresh());
			$view->addLaunchAction(Actions\CoverFlowActions::scrollTo(10,10), Action::LAUNCH_ACTION_VISIBLE);
			$view->addLaunchAction(Actions\CoverFlowActions::flip(), Action::LAUNCH_ACTION_VISIBLE);

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
