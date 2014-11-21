<?php
namespace Ignite\Modules\Sample;
use Ignite\Action;
use Ignite\Actions;
use Ignite\Element;
use Ignite\Module;
use Ignite\Application;
use Ignite\Views;
use Ignite\View;
use Symfony\Component\HttpFoundation\Request;
use Yosymfony\Toml\Toml;
use Ignite\Localization as L;

class Sample extends Module {

	public function views(Application $app) {
        $controllers = $app['controllers_factory'];

		$app->registerView('homepage', View::TYPE_WIDGET_VIEW, 'hello-{type}', function(Application $app, Request $args) {
			$v = new Views\WidgetView($app, 'homepage');

			//this static view will have no elements if no param is specified

			if ($args->get('type') == '1')
				$v->addImage('eq');
			else if ($args->get('type') == '2')
				$v->addImage('tq')->onTap(function(){
					Actions\WidgetActions::swapElementLocation('test', 'x', 0, 40, 0.3, 0, 100);
				});

			return $v;
		})->value('type', null);

		$app->registerView('alt_view', View::TYPE_WIDGET_VIEW, 'alt_view', function(Application $app) {
			$v = new Views\WidgetView($app, 'alt_view');
			$el = $v->addView($app->getView('homepage'), 'id2');
			$el['post_data'] = L::_('STRING1');

			return $v;
		});

        return $controllers;
    }
}
