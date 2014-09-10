<?php
namespace Ignite;

use Ignite\Actions\ActionBuffer;
use Symfony\Component\Config\Definition\Exception\InvalidTypeException;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

abstract class View extends Registry {

	//TODO: update all views to modify resource paths with the base path

	/**
	 *	Config spec for the Action Group element
	 */
	const ACTION_GROUP_SPEC				= 'action_group_elements.json';
	/**
	 *	Config spec for the Launch Actions element
	 */
	const LAUNCH_ACTIONS_SPEC			= 'launch_actions.json';
	/**
	 *	Config spec for the Buttons group element
	 */
	const BUTTON_ELEMENTS_SPEC			= 'button_elements.json';
	/**
	 *	Config spec for the Menu group element
	 */
	const MENU_ELEMENTS_SPEC			= 'menu_elements.json';
	/**
	 *	Config spec for the javascript functions element
	 */
	const JAVASCRIPT_ELEMENTS_SPEC		= 'javascript_elements.json';

	/**
	 *	Config spec for all generic actions
	 */
	const GENERIC_ACTIONS_SPEC			= 'generic_actions.json';

	/**
	 * @var ElementContainer[] Array containing all the container elements.
	 */
	protected $elementsContainers = [];

	/**
	 * @var ConfigContainer|null Element which contains the configuration of the view
	 */
	protected $config = null;

	/**
	 * @var array Contains the parsed config spec for the actions
	 */
	protected $actionsSpec = [];

	/**
	 * @var Application The Application instance where this view is contained.
	 */
	protected $app;
	/**
	 * @var ID of the view (avi)
	 */
	protected $viewID;

	/**
	 * @var bool Wether the view xml will
	 */
	protected $cacheable = false;

	/**
	 * @var int Cache expiration in seconds
	 */
	protected $cacheExpires = 3600;

	/**
	 * @var Processor Processor used for validating element properties
	 */
	public $processor = null;

	/**
	 *
	 * Constructs a new view which creates all containers.
	 *
	 * @param Application $app
	 * @param $viewID
	 */
	public function __construct(Application $app, $viewID) {
		parent::__construct('par');
		$this->viewID = $viewID;
		$this->processor = new Processor();
		$this->elementsContainers['action_groups'] = $this->appendChild(new ElementContainer(self::ACTION_GROUP_SPEC, 'ags'));
		$this->elementsContainers['action_groups']->view = $this;
		$this->elementsContainers['buttons'] = $this->appendChild(new ElementContainer(self::BUTTON_ELEMENTS_SPEC, 'bs'));
		$this->elementsContainers['buttons']->view = $this;
		$this->elementsContainers['launch_actions'] = $this->appendChild(new ElementContainer(self::LAUNCH_ACTIONS_SPEC, 'las'));
		$this->elementsContainers['launch_actions']->view = $this;
		$this->elementsContainers['visible_launch_actions'] = $this->appendChild(new ElementContainer(self::LAUNCH_ACTIONS_SPEC, 'vas'));
		$this->elementsContainers['visible_launch_actions']->view = $this;
		$this->elementsContainers['hidden_launch_actions'] = $this->appendChild(new ElementContainer(self::LAUNCH_ACTIONS_SPEC, 'has'));
		$this->elementsContainers['hidden_launch_actions']->view = $this;
		$this->elementsContainers['menus'] = $this->appendChild(new ElementContainer(self::MENU_ELEMENTS_SPEC, 'ms'));
		$this->elementsContainers['menus']->view = $this;
		$this->elementsContainers['javascript'] = $this->appendChild(new ElementContainer(self::JAVASCRIPT_ELEMENTS_SPEC, 'funcs'));
		$this->elementsContainers['javascript']->view = $this;

		$this->actionsSpec = json_decode(file_get_contents(LIB_ROOT_DIR.ConfigContainer::CONFIG_PATH.'/generic_actions.json'), true);

		$this->app = $app;

		$this->cacheExpires = $app['env']['memcache.expiration'];
	}

	public function setConfigurationValues(array $values) {
		$this->config->setProperties($values);
	}

	/**
	 *
	 * Parses the configuration file for the curent module
	 *
	 * @param $filepath
	 */
	protected function parseConfiguration($filepath) {
		$config = $this->app->scan($filepath)->getArray();
		$this->config->setProperties(array_merge($this->config->getProperties(), $config['cfg']));

		if (isset($config['landscape']))
			$this->config->addPrefixedProperties($config['landscape'], Element::$prefixes[Element::FOR_LANDSCAPE -1]);

		if (isset($config['tablet']))
			$this->config->addPrefixedProperties($config['tablet'], Element::$prefixes[Element::FOR_TABLET -1]);

		if (isset($config['android']))
			$this->config->addPrefixedProperties($config['android'], Element::$prefixes[Element::FOR_ANDROID -1]);

		if (isset($config['landscape_tablet']))
			$this->config->addPrefixedProperties($config['landscape_tablet'], Element::$prefixes[(Element::FOR_LANDSCAPE | Element::FOR_TABLET) -1]);

		if (isset($config['landscape_android']))
			$this->config->addPrefixedProperties($config['landscape_android'], Element::$prefixes[(Element::FOR_LANDSCAPE | Element::FOR_ANDROID) -1]);

		if (isset($config['tablet_android']))
			$this->config->addPrefixedProperties($config['tablet_android'], Element::$prefixes[(Element::FOR_TABLET | Element::FOR_ANDROID) -1]);

		if (isset($config['landscape_tablet_android']))
			$this->config->addPrefixedProperties($config['landscape_tablet_android'], Element::$prefixes[(Element::FOR_LANDSCAPE | Element::FOR_TABLET | Element::FOR_ANDROID) -1]);

	}

	/**
	 * @param Action $action
	 * @param string $type Type of the action: visible or hidden. Ommit this param for the default behaviour.
	 * @return bool|Registry The inserted action or false if action has invalid properties.
	 */
	public function addLaunchAction(Action $action, $type = null) {
		switch($type) {
			case Action::LAUNCH_ACTION_VISIBLE: {
				$wrapperTag = 'va';
				$where = 'visible_launch_actions';

				break;
			}
			case Action::LAUNCH_ACTION_HIDDEN: {
				$wrapperTag = 'ha';
				$where = 'hidden_launch_actions';

				break;
			}
			default: {
				$wrapperTag = 'la';
				$where = 'launch_actions';
			}
		}

		$action->setTag($wrapperTag);

		return $this->elementsContainers[$where]->appendChild($action);
	}

	/**
	 *
	 * Adds a menu.
	 *
	 * @param Element $menu Optional.
	 * @return Element returns the inserted element.
	 */
	public function addMenu(Element $menu = null) {
		if ($menu === null)
			$menu = new Element('m');

		$menu->setTag('m');
		$menu->view = $this;

		return $this->elementsContainers['menus']->appendChild($menu);
	}

	/**
	 *
	 * Adds an element to a specific menu.
	 *
	 * @param array|Element|null $element Array containing element properties or the element itself or null which
	 * creates an empty element.
	 * @param Element $menu The menu where the element will be.
	 * @return Element Returns the inserted element.
	 */
	public function addMenuElement($element = null, Element $menu) {
		if ($element == null)
			$element = new Element('me');
		else if (is_array($element))
			$element = new Element('me', $element);

		$element->view = $this;

		return $menu->appendChild($element);
	}

	/**
	 *
	 * Adds an action group. This method is called when adding multiple actions to a view. There's no need to call this
	 * method explicitily.
	 *
	 * @param Action[] $actions
	 * @param null|string $name If name is not supplied the action group can be called by its index.
	 * @return Element|boolean Returns the inserted action group or false if it already exists.
	 * @throws InvalidTypeException
	 */
	public function addActionGroup(array $actions, $name = null) {
		$actionGroup = new Element('ag');

		if (isset($name) && $this->actionGroupExists($name))
			return false;

		foreach ($actions as $a) {
			if ($this->validateAction($a)) {
				$a->setTag('age');
				$actionGroup->appendChild($a);
			} else
				throw new InvalidTypeException("Action '{$a->getName()}' is not a valid action.");
		}

		if (isset($name))
			$actionGroup['action_group_name'] = $name;

		$actionGroup->view = $this;

		return $this->elementsContainers['action_groups']->appendChild($actionGroup);
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	public function actionGroupExists($name) {
		foreach ($this->elementsContainers['action_groups'] as $c) {
			if ($c['name'] == $name)
				return true;
		}

		return false;
	}

	/**
	 *
	 * Adds a button group
	 *
	 * @param array|Element|null $group Array containing element properties or the element itself or null which
	 * creates an empty group.
	 * @return Element the inserted group.
	 */
	public function addButtonGroup($group = null) {
		if ($group == null)
			$group = new Element('bg');
		else if (!$group instanceof Element)
			$group = new Element('bg', $group);

		$group->setTag('bg');
		$group->view = $this;

		$this->elementsContainers['buttons']->appendChild($group);

		return $group;
	}

	/**
	 *
	 * Adds an element to a specific button group or outside groups.
	 *
	 * @param array|Element $button Array with element's properties or an element.
	 * @param Element|null $group The group where to insert. Optional if you don't want to insert it in a group.
	 * @return Element The inserted element
	 */
	public function addButtonElement($button, $group = null) {
		if (!$button instanceof Element)
			$button = new Element('b', $button);

		if ($group !== null) {
			$button->setTag('b');
			$group->appendChild($button);
		} else
			$this->elementsContainers['buttons']->appendChild($button);

		if (isset($button['button_image']) && strpos($button['button_image'], 'http') !== 0)
			$button['button_image'] = $this->app->getWebPath().$button['button_image'];

		$button->view = $this;

		return $button;
	}

	/**
	 *
	 * Parses a javascript file to insert javascript function elements.
	 *
	 * @param string $name Name of the javascript file
	 * @throws FileNotFoundException
	 */
	public function addJavascriptFile($name) {
		if (!is_readable(ASSETS_DIR.'/js/'.$name))
			throw new FileNotFoundException('File \''.ASSETS_DIR.'/js/'.$name.'\' not found or not readable.');

		$functions = [];

		$result = [];
		$bodyFunc = [];
		$fileContent = file_get_contents(ASSETS_DIR.'/js/'.$name);
		preg_match_all("/(?<=function)\\s+[A-Za-z0-9_]+\\s?\\(([a-zA-Z0-9_]+(,\\s? )?)*\\)/", $fileContent, $result);
		preg_match_all("/({([^{}]|(?R))*})/", $fileContent, $bodyFunc, PREG_OFFSET_CAPTURE);

		$result = $result[0];

		function parseResult($string) {
			$result = [];
			$string = trim(preg_replace('/\s+/', '', $string));
			$endNamePos = strpos($string, "(");

			$result['name'] = substr($string, 0, $endNamePos);
			$result['args'] = explode(',', substr($string, $endNamePos+1, strlen($string)-2-$endNamePos));

			return $result;
		}

		foreach ($result as $r) {
			$functions[] = parseResult($r);
		}

		$resultBody = [];

		foreach ($bodyFunc[0] as $k => $f) {
			if (($f[1]+strlen($f[0]) == strlen($fileContent)))
				$resultBody[] = $f[0];
			else if ($fileContent[$f[1]+strlen($f[0])] !== ')')
				$resultBody[] = $f[0];
			else
				array_splice($functions, $k, 1);
		}

		foreach($functions as $k => $f) {
			$jsContainer = new Element('func', ['fname' => $f['name'], 'fargs' => implode('::', $f['args']),
				'body' => rtrim(ltrim($resultBody[$k], '{'), '}')
			]);

			$this->elementsContainers['javascript']->appendChild($jsContainer);
		}
	}

	/**
	 *
	 * Checks if the action name exists in the config spec.
	 *
	 * @param Action $a
	 * @return bool
	 */
	public function validateAction(Action $a) {
		$name = $a->getName();

		return isset($this->actionsSpec[$name]);
	}

	/**
	 *
	 * Gets the view ID.
	 *
	 * @return mixed
	 */
	public function getID() {
		return $this->viewID;
	}

	/**
	 *
	 * Gets the application in which this view is contained.
	 *
	 * @return Application
	 */
	public function getApp() {
		return $this->app;
	}

	/**
	 *
	 * Enables or disables the external cache (memcache).
	 *
	 * @param boolean $cache
	 */
	public function setCache($cache) {
		$this->cacheable = $cache;
	}

	/**
	 *
	 * Sets the expiration duration for this view in seconds
	 *
	 * @param integer $exp
	 */
	public function setCacheExpiration($exp) {
		$this->cacheExpires = $exp;
	}

	public function setViewId($avi) {
		$this->viewID = $avi;
	}

	/**
	 * @param bool $update
	 * @return array
	 */
	public function render($update = false) {
		if ($this->app['env']['memcache.enabled'] == "true" && $this->cacheable) {
			$key = $this->app->getRouteName().$this->viewID;
			$extraIgnore = isset($this->app['env']['app.ignore_post']) ? $this->app['env']['app.ignore_post'] : [];

			$data = array_diff($_POST, array_merge(Application::getBlacklistPostKeys(), $extraIgnore));
			$key .= implode('', $data);
			$key = 'ignite'.$key;

			$cached = $this->app['memcache']->get(hash('sha1', $key));

			if ($this->app['memcache']->getResultCode() ==  \Memcached::RES_SUCCESS)
				return $cached;
		}

		if ($this->render_cache !== [] && $update == false)
			return $this->render_cache;

		$result = [];

		/**
		 * @var Registry $c
		 */
		foreach ($this->getIterator() as $c) {
			if ($c->isEmpty())
				continue;

			if ($c->getTag() !== null) {
				if (!isset($result[$c->getTag()]))
					$result[$c->getTag()] = [];

				$result[$c->getTag()][] = $c->render($update);
			}
			else
				$result = array_merge($result, $c->render($update));
		}

		if ($this->isRoot())
			$this->render_cache = [$this->tag => [$result]];
		else
			$this->render_cache = $result;

		if ($this->app['env']['memcache.enabled'] == "true" && $this->cacheable)
			$this->app['memcache']->set($key, $this->render_cache, $this->cacheExpires);

		return $this->render_cache;
	}

	public function getConfigurationValues() {
		return $this->config->getProperties();
	}

	/**
	 *
	 * Used for adding actions and action groups.
	 *
	 * @param string $k
	 * @param \Closure $v
	 */
	public function __set($k, $v) {
		$fresult = $v();

		if ($fresult instanceof Action)
			$fresult = [$fresult];

		if (is_array($fresult))
			$this->addActionGroup($fresult, $k);
	}

	public function offsetExists($k) {
		return in_array($k, ['elements', 'action_groups', 'buttons', 'launch_actions', 'visible_launch_actions', 'hidden_launch_actions', 'menus', 'javascript']);
	}

	public function offsetGet($k) {
		return $this->elementsContainers[$k];
	}

	public function offsetSet($k, $v) {
		return null;
	}

	public function offsetUnset($k) {
		return null;
	}

} 