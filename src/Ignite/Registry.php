<?php

namespace Ignite;

class Registry  {
    protected $_vars	= [];
	protected $renderCache = null;

	/**
	 * @var View
	 */
	protected $view		= null;
    
    public $wrapperTag 	= null;

    public function __construct($wrapperTag = null) {
        $this->wrapperTag = $wrapperTag;
    }
    
    public function render($update = false) {
	    if ($this->renderCache !== null && $update == false)
			return $this->renderCache;

		$vars = get_object_vars($this);
	    $result = array_merge([], $this->_vars);

	    unset($vars['_vars']);
	    unset($vars['action']);
		unset($vars['view']);
		unset($vars['app']);
	    unset($vars['wrapperTag']);

		foreach ($vars as $var) {
			if ($var instanceof Registry) {
				$subResult = $var->render();
				if ($subResult !== null)
					$result = array_merge($result, $subResult);
			} else if (is_array($var)) {
				$result = array_merge($result, $this->renderArray($var, $result));
			}
		}

	    if (count($result)) {
		    if ($this->wrapperTag !== null)
	        	$result = [$this->wrapperTag => $result];

			$this->renderCache = $result;

	        return $result;
        }

        return null;
    }

	protected function renderArray($arr, &$where) {
		foreach($arr as $key => $val) {
			if ($val instanceof Registry) {
				$render = $val->render();
				if (empty($render)) $render = [];

				if (is_int($key))
					$where[$key] = $render;
				else
					$where = array_merge($where, $render);

			}
			else if (is_array($val)) {
				$where[$key] = [];
				$where = array_merge($where[$key], $this->renderArray($val, $where[$key]));
			}
		}

		return $where;
	}

	public function setVars(array $arr) {
		$this->_vars = $arr;
	}

	public function getVars() {
		return isset($this->wrapperTag) ? [$this->wrapperTag => $this->_vars] : $this->_vars;
	}

	public function __set($key, $val) {
		$this->_vars[$key] = $val;
	}

	public function __get($key) {
		if (isset($this->_vars[$key]))
			return $this->_vars[$key];

		return null;
	}

}