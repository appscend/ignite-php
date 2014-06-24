<?php

namespace Ignite;

class Action extends Registry {
	public $prefix;
	public $name;
	public $parameters;
	public $targetId;
	public $delay;
	public $confirmationText;
	
	public $loginProvider;
	
	public $requiredPurchaseName;
	public $onItemNotPurchased;
	public $directlyPurchaseProduct;
	
	public $requiredSecureKey;
	public $requiredSecureValue;
	public $onSecureKeyValueNotFound;
	
	public $internetConnectionRequired;
	public $onInternetConnectionNotAvailable;
	
	function __destruct() {
		
	}
	
	function __call($name, $args) {
		if ($name == "assert" && count($args) == 2) {
			$this->assert($args[0], null, $args[1]);
		}
	}
	
	public function delay($duration) {
		$this->delay = intval($duration);
		return $this;
	}
	
	public function confirm($text) {
		if (!is_string($text))
			throw new \InvalidArgumentException('Confirmation text to action must be string');
		$this->confirmationText = $text;
		return $this;
	}
	
	public function assertApplicationKey($key, $value, \Closure $onNotFound = null) {
		if (!is_string($key))
			throw new \InvalidArgumentException('Assertion key on action must be string');
		$this->requiredSecureKey = $key;
		if (is_string($value))
			$this->requiredSecureValue = $value;
		if ($onNotFound !== null)
			$this->$onSecureKeyValueNotFound = $onNotFound;
		return $this;
	}
	
	public function assertInternetAvailable(\Closure $onConnectionUnavailable = null) {
		$this->internetConnectionRequired = true;
		if ($onConnectionUnavailable !== null)
			$this->onInternetConnectionNotAvailable = $onConnectionUnavailable;
		return $this;
	}
	
	public function assertProductPurchased($productName, \Closure $onNotPurchased = null) {
		if (!is_string($productName))
			throw new \InvalidArgumentException('Assertion product name on action must be string');
		$this->$directlyPurchaseProduct = true;
		$this->requiredPurchaseName = $productName;
		if ($onNotPurchased !== null)
			$this->onItemNotPurchased = $onNotPurchased;
		return $this;
	}
	
	public function load() {	
		if (!is_string($this->name))
			throw new \LogicException('Cannot create nameless action');
		if ($this->prefix === null) $prefix = "";
		else $prefix = $this->prefix;
		
		$this->_vars[$prefix."a"] = $this->name;
		if ($this->parameters !== null)
			$this->_vars[$prefix."pr"] = is_array($this->parameters)?implode("::", $this->parameters):(string)$this->parameters;
		if ($this->targetId !== null)
			$this->_vars[$prefix."tavi"] = $this->targetId;
		if ($this->delay !== null)
			$this->_vars[$prefix."del"] = $this->delay;
		if ($this->confirmationText !== null)
			$this->_vars[$prefix."conf"] = $this->confirmationText;
		if ($this->loginProvider !== null) {
			$this->_vars[$prefix."l"] = "yes";
			$this->_vars[$prefix."lp"] = $this->loginProvider;
		}
		if ($this->requiredPurchaseName !== null) {
			$this->_vars[$prefix."aprod"] = $this->requiredPurchaseName;
			$this->_vars[$prefix."dprod"] = "yes";
		}
		if ($this->requiredSecureKey !== null)
			$this->_vars[$prefix."rsk"] = $this->requiredSecureKey;
		if ($this->requiredSecureValue !== null)
			$this->_vars[$prefix."rsv"] = $this->requiredSecureValue;
		if ($this->internetConnectionRequired)
			$this->_vars[$prefix."ic"] = "yes";
			
	}
}