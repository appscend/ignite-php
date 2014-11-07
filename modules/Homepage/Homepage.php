<?php
namespace Ignite\Modules\Homepage;
use Ignite\Action;
use Ignite\Actions;
use Ignite\Element;
use Ignite\Module;
use Ignite\Application;
use Ignite\Views;
use Ignite\View;
use Yosymfony\Toml\Toml;

class Homepage extends Module {

	public function __construct(Application $app)  {
		parent::__construct($app);
		//$this->parsedLayout = Toml::parse(APP_ROOT_DIR.'/vendor/appscend/ignite-module/src/IgniteModule/config.toml');
	}

	public function views(Application $app) {
        $controllers = $app['controllers_factory'];


		$app->registerView('homepage', View::TYPE_WIDGET_VIEW, 'hello', function(Application $app, $args = []) {
			$v = new Views\WidgetView($app, 'homepage');

			if (isset($args['eq']))
				$v->addImage('eq');
			else if (isset($args['tq']))
				$v->addImage('tq')->onTap(function(){
					Actions\WidgetActions::swapElementLocation('test', 'x', 0, 40, 0.3, 0, 100);
				});

			return $v;
		});
        
        /*$controllers->match('bye', function (Application $app) {
            $view = new Views\CoverflowView($app, "endpage_cf");
			$view->setCache(true);

			$view->addImage(["image" => "Test section", "name" => "xxx"])->
				onTap(function() {
					Actions\Navigation::refresh();
				})->
				setFor(["image" => "umadbro"], Element::FOR_ANDROID | Element::FOR_LANDSCAPE);

			$view->addImage([
				"image" => "doesn't exist.png",
				"name" => " 239487 329023y4i2h342hg34"
			])
				->onTap(function() {
					Actions\CoverFlowActions::startSlideshow()->requiresLogin('fb');
					Actions\CoverFlowActions::flip();
					Actions\System::alert("bla")->on("test");
				}, 'test')
				->setFor(["name" => "this is not what you think"], Element::FOR_LANDSCAPE | Element::FOR_ANDROID | Element::FOR_TABLET);

			$view->addLaunchAction(Actions\Navigation::refresh());
			$view->addLaunchAction(Actions\CoverFlowActions::scrollTo(10,10), Action::LAUNCH_ACTION_VISIBLE);
			$view->addLaunchAction(Actions\CoverFlowActions::flip(), Action::LAUNCH_ACTION_VISIBLE);

			$menu = $view->addMenu();

			$view->addMenuElement(["text" => "asdadad"], $menu);
			$view->addMenuElement(["text" => "text 2"], $menu);

			$view->addButtonElement(["text" => "buton fara grup"]);
			$view->addButtonElement(["text" => "buton in grup"], $view->addButtonGroup(["position" => "right"])->setFor(["position" => "left"], Element::FOR_ANDROID));

			$view->addJavascriptFile("testfile.js");

            return $view;
        })->bind("endpage");*/

        return $controllers;
    }
}
