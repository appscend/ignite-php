<?php
namespace Ignite;

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

	public function requiresPurchase($bundleId, Action $notPurchasedAction = null, $displayStoreView = null) {
		$this[$this->prefix.'aprod'] = $bundleId;

		if (isset($displayStoreView))
			$this[$this->prefix.'dprod'] = 'yes';

		if (isset($action))
			$this[$this->prefix.'prod'] = $notPurchasedAction->getName();

		return $this;
	}

	public function requiresSecureKey(Action $securityFailedAction, $value = null) {
		$this[$this->prefix.'rsk'] = 'yes';
		$this[$this->prefix.'rs'] = $securityFailedAction->getName();

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

	public function getName() {
		return $this->name;
	}

	public function appendChild(Registry $r) {}

	public function render($update = false) {
		return $this->properties;
	}

} 