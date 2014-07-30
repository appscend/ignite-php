<?php
namespace Ignite;

use Ignite\Actions\ActionBuffer;

class Action extends Registry {

	const LAUNCH_ACTION_VISIBLE = 'visible';
	const LAUNCH_ACTION_HIDDEN = 'hidden';
	
	protected $prefix = '';
	protected $name = null;

	public function __construct($name, array $params = [], $prefix = '') {
		$this->name = $this[$prefix.'a'] = $name;
		$this->prefix = $prefix;

		if (!empty($params))
			$this[$prefix.'pr'] = join("::", $params);
	}

	public function requiresLogin($provider = null) {
		$this[$this->prefix.'l'] = 'yes';

		if (isset($provider))
			$this[$this->prefix.'lp'] = $provider;


		return $this;
	}

	public function requiresPurchase($bundleId, $displayStoreView = null) {
		$this[$this->prefix.'aprod'] = $bundleId;

		if (isset($displayStoreView))
			$this[$this->prefix.'dprod'] = 'yes';

		return $this;
	}

	public function requiresSecureKey($value = null) {
		$this[$this->prefix.'rsk'] = 'yes';

		if (isset($value))
			$this[$this->prefix.'rsv'] = $value;

		return $this;
	}

	public function confirmation($text) {
		$this[$this->prefix.'conf'] = $text;

		return $this;
	}

	public function delay($d) {
		$this[$this->prefix.'del'] = $d;

		return $this;
	}

	public function on($viewID) {
		$this[$this->prefix.'tavi'] = $viewID;

		return $this;
	}

	/**
	 * @param \Closure|Action $action
	 * @param string $name
	 * @param View $view
	 * @throws \InvalidArgumentException
	 */
	public function onProductCheckFail($action, $name = null, View $view = null) {
		if ($action instanceof \Closure) {
			$action();

			$fresult = ActionBuffer::getAndClearBuffer();

			if (!isset($fresult[1])) {
				$ac = $fresult[0];
				$ac->setPrefix($this->prefix.'prod');
			} else {
				$index = $view->addActionGroup($fresult, $name);
				if ($name !== null)
					$ac = new Action('pag:', [$name], $this->prefix.'prod');
				else
					$ac = new Action('pag:', [$index-1], $this->prefix.'prod');
			}
		} else if ($action instanceof Action) {
			$ac = $action;
			$ac->setPrefix($this->prefix.'prod');
		} else
			throw new \InvalidArgumentException("Parameter 1 for 'onProductCheckFail' must be instance of Action or Closure.");

		$this->properties = array_merge($this->properties, $ac->render());
	}

	/**
	 * @param \Closure|Action $action
	 * @param string $name
	 * @param View $view
	 * @throws \InvalidArgumentException
	 */
	public function onSecureKeyCheckFail($action, $name = null, View $view = null) {
		if ($action instanceof \Closure) {
			$action();

			$fresult = ActionBuffer::getAndClearBuffer();

			if (!isset($fresult[1])) {
				$ac = $fresult[0];
				$ac->setPrefix($this->prefix.'rs');
			} else {
				$index = $view->addActionGroup($fresult, $name);
				if ($name !== null)
					$ac = new Action('pag:', [$name], $this->prefix.'rs');
				else
					$ac = new Action('pag:', [$index-1], $this->prefix.'rs');
			}
		} else if ($action instanceof Action) {
			$ac = $action;
			$ac->setPrefix($this->prefix.'rs');
		} else
			throw new \InvalidArgumentException("Parameter 1 for 'onSecureKeyCheckFail' must be instance of Action or Closure.");

		$this->properties = array_merge($this->properties, $ac->render());
	}

	public function getName() {
		return $this->name;
	}

	public function setPrefix($p) {
		$this->prefix = $p;
	}

	public function appendChild(Registry $r) {}

	public function render($update = false) {
		return $this->properties;
	}

} 