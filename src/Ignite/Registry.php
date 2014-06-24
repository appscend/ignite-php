<?php

namespace Ignite;

Class Registry 
{
    protected $_vars;
    protected $_actions;
    protected $_closures;
    
    public $wrapperTag = null;
    public $actionContainer = null;

    public function __construct($wrapperTag = null) {
        $this->_vars = array();
        $this->_actions = array();
        $this->_closures = array();
        $this->wrapperTag = $wrapperTag;
    }

    public function __set($key, $val) {
    	if ($val instanceof Action) {
    		$this->_actions[substr($key, 0, -1)] = $val;
    	}
    	else if ($val instanceof \Closure) {
    		$this->_closures[$key] = $val;
    	}
        else {
        	$this->_vars[$key] = $val;
        }
    }
    
    public function setVars($array) {
	    $this->_vars = $array;
    }

    public function __get($key) {
        if (isset($this->_vars[$key]))
            return $this->_vars[$key];
    }
    
    public function load() {
	    
    }
    
    public function render() {
    	$this->load();
	    $vars = get_object_vars($this);	
	    $result = array_merge(array(), $this->_vars);
	    
	    unset($vars['_vars']);
	    unset($vars['_actions']);
	    unset($vars['wrapperTag']);
	    
	    foreach ($vars as $varName => $var) {
	    	if ($var instanceof Registry) {
	    		$subresult = $var->render();
	    		if ($subresult !== null)
		    		$result = array_merge($result, $subresult);
	    	}
	    	else if (is_array($var)) {
		    	foreach ($var as $containerName=>$value) {
		    		$objects = array();
			    	if (is_array($value))
			    		foreach ($value as $object)
			    			if ($object instanceof Registry) {
			    				(null !== $rendered = $object->render())?$objects[] = $rendered:null;
			    			}
			    	if (count($objects))
		    			$result = array_merge($result, [$containerName => [substr($containerName, 0, -1) => $objects]]);
		    	}
	    	}
	    }
	    
	    foreach ($this->_actions as $prefix => $action) {
		    $action->prefix = $prefix;
		    $renderedAction = $action->render();
		    if ($renderedAction !== null)
		    	$result = array_merge($result, $renderedAction);
	    }
	    
	    if (count($result)) {
		    if ($this->wrapperTag !== null)
	        	return [$this->wrapperTag => $result];
	        else 
	        	return $result;
        }
        else
        	return null;
    }

    public function varsToArray() {
    	if ($this->wrapperTag !== null)
        	return [$this->wrapperTag => $this->_vars];
        else 
        	return $this->_vars;
    }
    
    public function actions() {
	    return $this->_actions;
    }
}