<?php
namespace Ignite;

use Ignite\Actions\ActionBuffer;

class Action extends Registry {

	/**
	 *	Visible launch actions are ran after returning to the view
	 */
	const LAUNCH_ACTION_VISIBLE = 'visible';
	/**
	 *	Hidden launch actions are ran after leaving the current view
	 */
	const LAUNCH_ACTION_HIDDEN = 'hidden';

	/**
	 * @var string Action prefix
	 */
	protected $prefix = '';
	/**
	 * @var string Action name as it appears in the XML doc
	 */
	protected $name = null;

	/**
	 * Instatiates a new action. Actions created this way are not automatically added in the Actions Buffer.
	 * Use the specific static classes for this.
	 *
	 * @param string $name Action name as it appears in the XML doc
	 * @param array $params Parameters for the action
	 * @param string $prefix Action prefix
	 */
	public function __construct($name, array $params = [], $prefix = '') {
		$this->name = $this[$prefix.'a'] = $name;
		$this->prefix = $prefix;

		if (!empty($params))
			$this[$prefix.'pr'] = rtrim(join("::", $params), ':');

	}

	/**
	 *
	 * The action will be performed only if the user is logged in.
	 *
	 * @param string $provider Either 'fid' for facebook or 'tid' for twitter.
	 * @return $this This instance useful for chaining methods.
	 */
	public function requiresLogin($provider = null) {
		$this[$this->prefix.'l'] = 'yes';

		if (isset($provider))
			$this[$this->prefix.'lp'] = $provider;


		return $this;
	}

	/**
	 *
	 * The action will be performed only if the user has made a previous purchase (indicated by the bundle id).
	 *
	 * @param $bundleId
	 * @param boolean $displayStoreView Proceed to product purchase immediately, without displaying store view
	 * @return $this This instance useful for chaining methods.
	 */
	public function requiresPurchase($bundleId, $displayStoreView = false) {
		$this[$this->prefix.'aprod'] = $bundleId;
		$this[$this->prefix.'dprod'] = $displayStoreView == true ? 'yes' : 'no';

		return $this;
	}

	/**
	 *
	 * Require that a secure key be set to perform the action
	 *
	 * @param string $value Require that a certain value be stored for the secure key. If this is not given only the
	 * presence of the stored key is checked.
	 * @return $this This instance useful for chaining methods.
	 */
	public function requiresSecureKey($value = null) {
		$this[$this->prefix.'rsk'] = 'yes';

		if (isset($value))
			$this[$this->prefix.'rsv'] = $value;

		return $this;
	}

	/**
	 *
	 * The text inside will be displayed together with a confirmation dialog before performing the action
	 *
	 * @param string $text Confirmation dialog text
	 * @return $this This instance useful for chaining methods.
	 */
	public function confirmation($text) {
		$this[$this->prefix.'conf'] = $text;

		return $this;
	}

	/**
	 *
	 * Action execution delay, in seconds
	 *
	 * @param integer $d Duration
	 * @return $this This instance useful for chaining methods.
	 */
	public function delay($d) {
		$this[$this->prefix.'del'] = $d;

		return $this;
	}

	/**
	 *
	 * ID of the view to perform this action upon.
	 *
	 * @param $viewID
	 * @return $this This instance useful for chaining methods.
	 */
	public function on($viewID) {
		$this[$this->prefix.'tavi'] = $viewID;

		return $this;
	}

	/**
	 *
	 * Action to execute in case the <prod> check fails.
	 *
	 * @param \Closure|Action $action Closure which contains calls to the static Action classes or just one action.
	 * @param string $name In case multiple actions are present, specificy a name for the action group. Defaults to
	 * the index of the group after insertion.
	 * @param View $view The view in which the action group will be added
	 * @throws \InvalidArgumentException If $action is not an instance of \Closure or Action
	 */
	public function onProductCheckFail($action, $name = null, View $view = null) {
		if ($action instanceof \Closure) {
			$action();

			$fresult = ActionBuffer::getAndClearBuffer();

			if (!isset($fresult[1])) {
				$ac = $fresult[0];
				$ac->setPrefix($this->prefix.'prod');
			} else {

				$el = $view->addActionGroup($fresult, $name);

				if ($name !== null)
					$ac = new Action('pag:', [$name], $this->prefix.'prod');
				else {
					$index = $view['action_groups']->getChildIndex($el);
					$ac = new Action('pag:', [$index-1], $this->prefix.'prod');
				}
			}
		} else if ($action instanceof Action) {
			$ac = $action;
			$ac->setPrefix($this->prefix.'prod');
		} else
			throw new \InvalidArgumentException("Parameter 1 for 'onProductCheckFail' must be instance of Action or Closure.");

		$this->properties = array_merge($this->properties, $ac->render());
	}

	/**
	 *
	 * Action to perform in case that the <rsk> check failed
	 *
	 * @param \Closure|Action $action Closure which contains calls to the static Action classes or just one action.
	 * @param string $name In case multiple actions are present, specificy a name for the action group. Defaults to
	 * the index of the group after insertion.
	 * @param View $view The view in which the action group will be added
	 * @throws \InvalidArgumentException If $action is not an instance of \Closure or Action
	 */
	public function onSecureKeyCheckFail($action, $name = null, View $view = null) {
		if ($action instanceof \Closure) {
			$action();

			$fresult = ActionBuffer::getAndClearBuffer();

			if (!isset($fresult[1])) {
				$ac = $fresult[0];
				$ac->setPrefix($this->prefix.'rs');
			} else {

				$el = $view->addActionGroup($fresult, $name);

				if ($name !== null)
					$ac = new Action('pag:', [$name], $this->prefix.'rs');
				else {
					$index = $view['action_groups']->getChildIndex($el);
					$ac = new Action('pag:', [$index-1], $this->prefix.'rs');
				}
			}
		} else if ($action instanceof Action) {
			$ac = $action;
			$ac->setPrefix($this->prefix.'rs');
		} else
			throw new \InvalidArgumentException("Parameter 1 for 'onSecureKeyCheckFail' must be instance of Action or Closure.");

		$this->properties = array_merge($this->properties, $ac->render());
	}

	/**
	 *
	 * Returns the name of the Action.
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 *
	 * Sets a prefix for the action
	 *
	 * @param string $p
	 */
	public function setPrefix($p) {
		$this->prefix = $p;
	}

	/**
	 *
	 * Actions cannot have children.
	 *
	 * @param Registry $r
	 */
	public function appendChild(Registry $r) {}

	/**
	 *
	 * Actions cannot have children.
	 *
	 * @param Registry $r
	 */
	public function prependChild(Registry $r) {}

	/**
	 *
	 * Renders the current action.
	 *
	 * @param bool $update
	 * @return array
	 */
	public function render($update = false) {
		return $this->properties;
	}

} 