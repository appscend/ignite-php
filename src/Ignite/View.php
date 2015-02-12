<?php
namespace Ignite;

use Ignite\Actions\ActionBuffer;
use Ignite\Helpers\XmlDomConstruct;
use Ignite\Providers\Logger;
use Symfony\Component\Config\Definition\Exception\InvalidTypeException;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

abstract class View extends Registry {

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

	const TYPE_CAMERA_VIEW		=	0;
	const TYPE_GRID_VIEW		=	1;
	const TYPE_FORM_VIEW		=	2;
	const TYPE_LAYAR_VIEW		=	3;
	const TYPE_MAP_VIEW			=	4;
	const TYPE_MENUBAR_VIEW		=	5;
	const TYPE_QRCODE_VIEW		=	6;
	const TYPE_TABBAR_VIEW		=	7;
	const TYPE_WEBVIEW_VIEW		=	8;
	const TYPE_WIDGET_VIEW		=	9;

	/**
	 * @var Element[]
	 */
	protected $elementClasses = [];

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

	protected $pathParameters = [
		'background_image',
		'background_image_landscape',
		'navigation_bar_image',
		'navigation_bar_image_landscape',
		'navigation_bar_image_logo',
		'toolbar_bg_image'
	];

	/**
	 * @var \Closure[]
	 */
	protected $actionClosures = [];

	/**
	 * @var Processor Processor used for validating element properties
	 */
	public $processor = null;

	/**
	 *
	 * Constructs a new view which creates all containers.
	 *
	 * @param Application $app
	 * @param string|nul $viewID null if it's an empty view (no elements or config containers)
	 */
	public function __construct(Application $app, $viewID = null) {
		parent::__construct('par');

		if ($viewID !== null && $app->getView($viewID) === null) {
			throw new ResourceNotFoundException("View with ID '$viewID' does not exist.");
		}

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

		if (isset($app['env']['memcache.enabled']) && $app['env']['memcache.enabled'] == 'true')
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
	protected function parseConfiguration() {
		$avi = $this->viewID;

		$allConfig = $this->processGlobalConfig();

		$allConfig = Module::array_merge_recursive_distinct($allConfig, $this->array_filter_key($this->app->getCurrentModule()->getLayout(), function($k) use ($avi){ return strpos($k, $avi) === 0; }));

		$allConfig[$avi] = array_filter($allConfig[$avi], function($v){ return !is_array($v); });
		$this->processAssetsPaths($allConfig[$avi], $this->pathParameters);
		$this->config->appendProperties($allConfig[$avi]);

		if (isset($allConfig[$avi.'*l'])) {
			$allConfig[$avi.'*l'] = array_filter($allConfig[$avi.'*l'], function($v){ return !is_array($v); });
			$this->processAssetsPaths($allConfig[$avi.'*l'], $this->pathParameters);
			$this->config->addPrefixedProperties($allConfig[$avi.'*l'], Element::$prefixes[Element::FOR_LANDSCAPE -1]);
		}

		if (isset($allConfig[$avi.'*tab'])) {
			$allConfig[$avi.'*tab'] = array_filter($allConfig[$avi.'*tab'], function($v){ return !is_array($v); });
			$this->processAssetsPaths($allConfig[$avi.'*tab'], $this->pathParameters);
			$this->config->addPrefixedProperties($allConfig[$avi.'*tab'], Element::$prefixes[Element::FOR_TABLET -1]);
		}

		if (isset($allConfig[$avi.'*and'])) {
			$allConfig[$avi.'*and'] = array_filter($allConfig[$avi.'*and'], function($v){ return !is_array($v); });
			$this->processAssetsPaths($allConfig[$avi.'*and'], $this->pathParameters);
			$this->config->addPrefixedProperties($allConfig[$avi.'*and'], Element::$prefixes[Element::FOR_ANDROID -1]);
		}

		if (isset($allConfig[$avi.'*tabl'])) {
			$allConfig[$avi.'*tabl'] = array_filter($allConfig[$avi.'*tabl'], function($v){ return !is_array($v); });
			$this->processAssetsPaths($allConfig[$avi.'*tabl'], $this->pathParameters);
			$this->config->addPrefixedProperties($allConfig[$avi.'*tabl'], Element::$prefixes[(Element::FOR_LANDSCAPE | Element::FOR_TABLET) -1]);
		}

		if (isset($allConfig[$avi.'*andl'])) {
			$allConfig[$avi.'*andl'] = array_filter($allConfig[$avi.'*andl'], function($v){ return !is_array($v); });
			$this->processAssetsPaths($allConfig[$avi.'*andl'], $this->pathParameters);
			$this->config->addPrefixedProperties($allConfig[$avi.'*andl'], Element::$prefixes[(Element::FOR_LANDSCAPE | Element::FOR_ANDROID) -1]);
		}

		if (isset($allConfig[$avi.'*andtab'])) {
			$allConfig[$avi.'*andtab'] = array_filter($allConfig[$avi.'*andtab'], function($v){ return !is_array($v); });
			$this->processAssetsPaths($allConfig[$avi.'*andtab'], $this->pathParameters);
			$this->config->addPrefixedProperties($allConfig[$avi.'*andtab'], Element::$prefixes[(Element::FOR_TABLET | Element::FOR_ANDROID) -1]);
		}

		if (isset($allConfig[$avi.'*andtabl'])) {
			$allConfig[$avi.'*andtabl'] = array_filter($allConfig[$avi.'*andtabl'], function($v){ return !is_array($v); });
			$this->processAssetsPaths($allConfig[$avi.'*andtabl'], $this->pathParameters);
			$this->config->addPrefixedProperties($allConfig[$avi.'*andtabl'], Element::$prefixes[(Element::FOR_LANDSCAPE | Element::FOR_TABLET | Element::FOR_ANDROID) -1]);
		}

		if (isset($allConfig[$avi.'*ff4'])) {
			$allConfig[$avi.'*ff4'] = array_filter($allConfig[$avi.'*ff4'], function($v){ return !is_array($v); });
			$this->processAssetsPaths($allConfig[$avi.'*ff4'], $this->pathParameters);
			$this->config->addPrefixedProperties($allConfig[$avi.'*ff4'], Element::$prefixes[(Element::FOR_FF4) -1]);
		}

		if (isset($allConfig[$avi.'*ff4l'])) {
			$allConfig[$avi.'*ff4l'] = array_filter($allConfig[$avi.'*ff4l'], function($v){ return !is_array($v); });
			$this->processAssetsPaths($allConfig[$avi.'*ff4l'], $this->pathParameters);
			$this->config->addPrefixedProperties($allConfig[$avi.'*ff4l'], Element::$prefixes[(Element::FOR_FF4 | Element::FOR_LANDSCAPE) -1]);
		}

		if (isset($allConfig[$avi.'*ff4pad'])) {
			$allConfig[$avi.'*ff4pad'] = array_filter($allConfig[$avi.'*ff4pad'], function($v){ return !is_array($v); });
			$this->processAssetsPaths($allConfig[$avi.'*ff4pad'], $this->pathParameters);
			$this->config->addPrefixedProperties($allConfig[$avi.'*ff4pad'], Element::$prefixes[(Element::FOR_FF4 | Element::FOR_TABLET) -1]);
		}

		if (isset($allConfig[$avi.'*ff4padl'])) {
			$allConfig[$avi.'*ff4padl'] = array_filter($allConfig[$avi.'*ff4padl'], function($v){ return !is_array($v); });
			$this->processAssetsPaths($allConfig[$avi.'*ff4padl'], $this->pathParameters);
			$this->config->addPrefixedProperties($allConfig[$avi.'*ff4padl'], Element::$prefixes[(Element::FOR_FF4 | Element::FOR_TABLET | Element::FOR_LANDSCAPE) -1]);
		}
	}

	protected function processGlobalConfig() {
		$cfg = $this->array_filter_key($this->app->getCurrentModule()->getLayout(), function($k) { return $k[0] === '$'; });
		$result = [];

		foreach ($cfg as $k => $v) {
			$key = str_replace('$global', $this->viewID, $k);
			$result[$key] = $v;
		}

		return $result;
	}

	/**
	 * @param \Closure|Action $action
	 * @param string $type Type of the action: visible or hidden. Ommit this param for the default behaviour.
	 * @return bool|Registry The inserted action or false if action has invalid properties.
	 */
	public function addLaunchAction($action, $type = null, $name = null) {
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

		if ($action instanceof \Closure) {
			$action();
			$fresult = ActionBuffer::getBuffer();

			if (!isset($fresult[1])) {
				$fresult[0]->setTag($wrapperTag);
				$action = $fresult[0];
				ActionBuffer::clearBuffer();

				return $this->elementsContainers[$where]->appendChild($action);
			} else {
				$el = $this->addActionGroup($fresult, $name);

				if ($name !== null) {
					$action = new Action('pag:', [$name]);
					$action->setTag($wrapperTag);

					return $this->elementsContainers[$where]->appendChild($action);
				}
				else {
					$index = $this['action_groups']->getChildIndex($el);

					$action = new Action('pag:', [$index]);
					$action->setTag($wrapperTag);

					return $this->elementsContainers[$where]->appendChild($action);
				}
			}
		} else if ($action instanceof Action) {
			$action->setTag($wrapperTag);
			return $this->elementsContainers[$where]->appendChild($action);
		} else
			throw new InvalidTypeException('Invalid argument 1. Must be instance of Action or \Closure');
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
	 * @param array|null $element Array containing element properties or the element itself or null which
	 * creates an empty element.
	 * @param Element $menu The menu where the element will be.
	 * @return Element Returns the inserted element.
	 */
	public function addMenuElement($key, $props = [], Element $menu) {
		$element = new Element('me');

		if ($key) {
			$element['Key'] = $key;
			$keys = explode(',', $key);

			foreach ($keys as $k) {
				if (isset($this->elementClasses[$k])) {
					$this->applyProperties($element, $this->elementClasses[$k]);
				} else {
					$this->app['ignite_logger']->log("Class '$k' is not defined in config file, in view '{$this->viewID}'.", Logger::LOG_WARN);

					return false;
				}
			}

		}

		$element->appendProperties($props);
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
		ActionBuffer::clearBuffer();

		return $this->elementsContainers['action_groups']->appendChild($actionGroup);
	}

	public function getActionGroup($name, $prefix = '') {
		if ($this->actionGroupExists($name))
			return new Action('pag', [$name], $prefix);

		return null;
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	public function actionGroupExists($name) {
		foreach ($this->elementsContainers['action_groups'] as $c) {
			if (isset($c['action_group_name']) && $c['action_group_name'] == $name)
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
	public function addButtonGroup($key = null, $group = null) {
		if ($group == null)
			$group = new Element('bg');
		else if (!$group instanceof Element)
			$group = new Element('bg', $group);

		if ($key) {
			$group['Key'] = $key;
			$keys = explode(',', $key);

			foreach ($keys as $k) {
				if (isset($this->elementClasses[$k])) {
					$this->applyProperties($group, $this->elementClasses[$k]);
				} else {
					$this->app['ignite_logger']->log("Class '$k' is not defined in config file, in view '{$this->viewID}'.", Logger::LOG_WARN);

					return false;
				}
			}
		}

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
	public function addButtonElement($key = null, $button = null, $group = null) {
		if (!$button instanceof Element)
			$button = new Element('b', $button);

		if ($group !== null) {
			$button->setTag('b');
			$group->appendChild($button);
		} else
			$this->elementsContainers['buttons']->appendChild($button);

		if ($key) {
			$button['Key'] = $key;
			$keys = explode(',', $key);

			foreach ($keys as $k) {
				if (isset($this->elementClasses[$k])) {
					$this->applyProperties($button, $this->elementClasses[$k]);
				} else {
					$this->app['ignite_logger']->log("Class '$k' is not defined in config file, in view '{$this->viewID}'.", Logger::LOG_WARN);

					return false;
				}
			}
		}

		if (isset($button['button_image']) && strpos($button['button_image'], 'http') !== 0)
			$button['button_image'] = $this->app->getAssetsPath().$button['button_image'];

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

	/**
	 * @param bool $update
	 * @return array
	 */
	public function render($update = false) {
		if (isset($this->app['env']['memcache.enabled']) && $this->app['env']['memcache.enabled'] == "true" && $this->cacheable) {
			$key = $this->app->getRouteName().$this->viewID;
			$extraIgnore = isset($this->app['env']['app.ignore_post']) ? $this->app['env']['app.ignore_post'] : [];

			$data = array_diff($_POST, array_merge(Application::getBlacklistPostKeys(), $extraIgnore));
			$key .= implode('', $data);
			$key = 'ignite'.$key;

			$cached = $this->app['memcache']->get(hash('sha1', $key));

			if ($this->app['memcache']->getResultCode() ==  \Memcached::RES_SUCCESS && $cached !== false)
				return $cached;
		}

		if ($this->render_cache !== [] && $update == false)
			return $this->render_cache;

		$result = [];

		$this->resolveActionClosures();

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

		if (isset($this->app['env']['memcache.enabled']) && $this->app['env']['memcache.enabled'] == "true" && $this->cacheable) {
			$xml = new XmlDomConstruct('1.0', 'UTF-8');
			$xml->fromMixed($this->render_cache);

			$this->app['memcache']->set($key, $xml->saveXML(null, LIBXML_NOEMPTYTAG), $this->cacheExpires);
		}

		return $this->render_cache;
	}

	private function resolveActionClosures() {
		foreach ($this->actionClosures as $name => $c) {
			if ($this->actionGroupExists($name))
				continue;

			$c();
			$this->addActionGroup(ActionBuffer::getBuffer(), $name);
		}
	}

	protected function getElementsFromConfig() {
		$classes = array_filter($this->app->getCurrentModule()->getLayout()[$this->viewID], function($k) { return is_array($k); });
		$result = [];

		$prefixConstants = array_flip(Element::$prefixes);

		foreach ($classes as $key => $props) {
			$r = explode('*', $key); // r[0] - keyname; r[1] = prefix (may not exist);

			if (!isset($result[$r[0]]))
				$result[$r[0]] = [];

			if (!isset($r[1])) {
				$result[$r[0]][0] = $props;
			} else {
				$result[$r[0]][$prefixConstants[$r[1]]+1] = $props;
			}

		}

		$result = Module::array_merge_recursive_distinct($result, $this->getGlobalClasses());
		$this->elementClasses = $result;
	}

	private function getGlobalClasses() {
		$globalClasses = $this->array_filter_key($this->app->getCurrentModule()->getLayout(), function($k) { return $k[0] === '@'; });

		$prefixConstants = array_flip(Element::$prefixes);
		$result = [];

		foreach ($globalClasses as $key => $props) {
			$p = explode('*', $key); // p[0] - keyname; p[1] = prefix (may not exist);
			$p[0] = trim($p[0], '@');

			if (!isset($result[$p[0]]))
				$result[$p[0]] = [];

			if (!isset($p[1])) {
				$result[$p[0]][0] = $props;
			} else {
				$result[$p[0]][$prefixConstants[$p[1]]+1] = $props;
			}
		}

		return $result;
	}

	protected function processAssetsPaths(array &$array, array $pathParameters) {
		foreach ($pathParameters as $p) {
			//values that start with '[' are placeholder values
			if (isset($array[$p]) && strpos($array[$p], 'http') !== 0 && $array[$p][0] != '[')
				$array[$p] = $this->app->getAssetsPath().$array[$p];
		}
	}

	protected function applyProperties(Element $el, array $props) {
		if (isset($props[0]))
			$el->appendProperties($props[0]);

		foreach ($props as $prefix => $pr) {
			if ($prefix == 0)
				continue;
			$el->setFor($pr, $prefix);
		}
	}

	protected function array_filter_key(array $a, \Closure $f) {
		$f = array_filter(array_keys($a), $f);

		return array_intersect_key($a, array_flip($f));
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
		if ($v instanceof \Closure) {
			$this->actionClosures[$k] = $v;
			$v();
			$this->addActionGroup(ActionBuffer::getBuffer(), $k);
		}
	}

	public function __get($k) {
		if (isset($this->actionClosures[$k]))
			return $this->actionClosures[$k];

		return null;
	}

	public function __call($name, $param) {
		if (isset($this->actionClosures[$name])) {
			$f = $this->actionClosures[$name];

			return $f();
		}

		throw new \InvalidArgumentException("View with id '{$this->viewID}' has no group action named '$name'");
	}

	public function offsetExists($k) {
		return in_array($k, ['elements', 'action_groups', 'buttons', 'launch_actions', 'visible_launch_actions', 'hidden_launch_actions', 'menus', 'javascript', 'config']);
	}

	public function offsetGet($k) {
		return $k == 'config' ? $this->config : $this->elementsContainers[$k];
	}

	public function offsetSet($k, $v) {
		return null;
	}

	public function offsetUnset($k) {
		return null;
	}

} 