<?php

namespace Ignite;


class Action extends Registry{

	private $prefix;
	private $actionName;
	private $render = [];

	public function __construct($actionName, array $params = null, $prefix = '') {
		$this->prefix = $prefix;
		$this->render[$prefix.'a'] = $this->actionName = $actionName;

		if (isset($params))
			$this->render[$prefix.'pr'] = join(":", $params);
	}

	public function requiresLogin($provider = null) {
		$this->render[$this->prefix.'l'] = 'yes';

		if (isset($provider))
			$this->render[$this->prefix.'lp'] = $provider;


		return $this;
	}

	public function requiresPurchase($bundleId, Action $notPurchasedAction = null, $displayStoreView = null) {
		$this->render[$this->prefix.'aprod'] = $bundleId;

		if (isset($displayStoreView))
			$this->render[$this->prefix.'dprod'] = 'yes';

		if (isset($action))
			$this->render[$this->prefix.'prod'] = $notPurchasedAction->getName();

		return $this;
	}

	public function requiresSecureKey(Action $securityFailedAction, $value = null) {
		$this->render[$this->prefix.'rsk'] = 'yes';
		$this->render[$this->prefix.'rs'] = $securityFailedAction->getName();

		if (isset($value))
			$this->render[$this->prefix.'rsv'] = $value;

		return $this;
	}

	public function confirmation($text) {
		$this->render[$this->prefix.'conf'] = $text;

		return $this;
	}

	public function delay($d) {
		$this->render[$this->prefix.'del'] = $d;

		return $this;
	}

	public function on($viewID) {
		$this->render[$this->prefix.'tavi'] = $viewID;

		return $this;
	}

	public function getName() {
		return $this->actionName;
	}

	public function render() {
		return $this->render;
	}

} 