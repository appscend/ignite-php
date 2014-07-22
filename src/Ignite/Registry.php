<?php

namespace Ignite;

abstract class Registry implements \ArrayAccess, \Countable {

	protected $render_cache = [];
	/**
	 * @var Registry[]
	 */
	protected $children		= [];
	protected $properties	= [];
	protected $tag			= null;
	protected $parent		= null;

	public abstract function render($update = false);

	public function __construct($tag = null) {
		$this->tag = $tag;
	}

	public function appendChild(Registry $child) {
		$this->children[] = $child;
		$child->setParent($this);

		return $child;
	}

	/**
	 * @param Registry $child
	 * @return mixed
	 */
	public function prependChild(Registry $child) {
		array_unshift($this->children, $child);
		$child->setParent($this);

		return $child;
	}

	public function removeChild($idx) {
		if (isset($this->children[$idx])) {
			array_splice($this->children, $idx, 1);

			return true;
		}

		return false;
	}

	public function setParent(Registry $parent) {
		$this->parent = $parent;
	}

	public function getTag() {
		return $this->tag;
	}

	public function getChild($idx) {
		return isset($this->children[$idx]) ? $this->children[$idx] : null;
	}

	public function getChildren() {
		return $this->children;
	}

	public function getParent() {
		return $this->parent;
	}

	public function getProperties() {
		return $this->properties;
	}

	public function setProperties(array $p) {
		$this->properties = $p;
	}

	public function setTag($t) {
		$this->tag = $t;
	}

	public function isRoot() {
		return $this->parent === null;
	}

	public function isEmpty() {
		return !isset($this->children[0]) && empty($this->properties);
	}

	public function count() {
		return count($this->children);
	}

	public function offsetExists($k) {
		return isset($this->properties[$k]);
	}

	public function offsetGet($k) {
		return $this->properties[$k];
	}

	public function offsetSet($k, $v) {
		if (is_string($k) || !is_object($v))
			$this->properties[$k] = $v;
	}

	public function offsetUnset($k) {
		unset($this->properties[$k]);
	}
} 