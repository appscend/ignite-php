<?php

namespace Ignite;

use Traversable;

abstract class Registry implements \ArrayAccess, \Countable, \IteratorAggregate {

	/**
	 * @var array Containes the rendered element. Useful when content doesn't change after the first render.
	 */
	protected $render_cache = [];
	/**
	 * @var Registry[]
	 */
	protected $children		= [];
	/**
	 * @var array
	 */
	protected $properties	= [];
	/**
	 * @var array The prefixed properties
	 */
	protected $prefix_properties = [];
	/**
	 * @var string
	 */
	protected $tag			= null;
	/**
	 * @var Registry
	 */
	protected $parent		= null;

	/**
	 * @var array The prefixed ordered according to the mask
	 */
	public static $prefixes = [
		'l',		//0001
		'pad',		//0010
		'padl',		//0011
		'and',		//0100
		'andl',		//0101
		'andpad',	//0110
		'andpadl',	//0111
		'ff4',		//1000
		'ff4l',		//1001
		'ff4pad',	//1010
		'ff4padl'	//1011
	];

	/**
	 *
	 * Renders the element. Any object that has a representation in the xml must implement this method
	 *
	 * @param bool $update
	 * @return array
	 */
	public abstract function render($update = false);

	/**
	 * @param string $tag
	 */
	public function __construct($tag = null) {
		$this->tag = $tag;
	}

	/**
	 *
	 * Gets the index of a child.
	 *
	 * @param Registry $c
	 * @return integer|boolean
	 */
	public function getChildIndex(Registry $c) {
		return array_search($c, $this->children, true);
	}

	/**
	 *
	 * Inserts an element at the beginning
	 *
	 * @param Registry $child
	 * @return Registry The inserted element
	 */
	public function appendChild(Registry $child) {
		$this->children[] = $child;
		$child->setParent($this);

		return $child;
	}

	/**
	 *
	 * Inserts an element at the end
	 *
	 * @param Registry $child
	 * @return Registry The inserted element
	 */
	public function prependChild(Registry $child) {
		array_unshift($this->children, $child);
		$child->setParent($this);

		return $child;
	}

	/**
	 *
	 * Replaces a child at specified index with a new one.
	 *
	 * @param Registry $child
	 * @param int $idx Index of the child which will be replaced
	 * @return bool|Registry The replaced child or false if not found.
	 */
	public function replaceChild(Registry $child, $idx) {
		if (isset($this->children[$idx])) {
			$this->children[$idx] = $child;
			$child->setParent($this);

			return $child;
		}

		return false;
	}

	/**
	 *
	 * Removes a child at specified index.
	 *
	 * @param $idx
	 * @return bool True if child has been removed, false if not found.
	 */
	public function removeChild($idx) {
		if (isset($this->children[$idx])) {
			array_splice($this->children, $idx, 1);

			return true;
		}

		return false;
	}

	/**
	 *
	 * Sets a parent for this element. This method should be called when adding elements to another element, otherwise
	 * the hierarchy will not be maintained.
	 *
	 * @param Registry $parent
	 */
	public function setParent(Registry $parent) {
		$this->parent = $parent;
	}

	/**
	 * @return string
	 */
	public function getTag() {
		return $this->tag;
	}

	/**
	 * @param $idx
	 * @return Registry|null
	 */
	public function getChild($idx) {
		return isset($this->children[$idx]) ? $this->children[$idx] : null;
	}

	/**
	 * @return Registry[]
	 */
	public function getChildren() {
		return $this->children;
	}

	/**
	 * @return Registry
	 */
	public function getParent() {
		return $this->parent;
	}

	/**
	 * @return array
	 */
	public function getProperties() {
		return $this->properties;
	}

	/**
	 * @param array $p
	 */
	public function setProperties(array $p) {
		$this->properties = $p;
	}

	/**
	 * @param array $p
	 */
	public function appendProperties(array $p) {
		$this->properties = array_merge($this->properties, $p);
	}

	/**
	 * @return array
	 */
	public function getPrefixedProperties() {
		return $this->prefix_properties;
	}

	/**
	 * @param string $t
	 */
	public function setTag($t) {
		$this->tag = $t;
	}

	/**
	 * @return bool
	 */
	public function isRoot() {
		return $this->parent === null;
	}

	/**
	 * @return bool True if element has no properties and no children, false otherwise.
	 */
	public function isEmpty() {
		return !isset($this->children[0]) && empty($this->properties);
	}

	/**
	 *
	 * Counts the number of children.
	 *
	 * @return int
	 */
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

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Retrieve an external iterator
	 *
	 * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
	 * @return Traversable An instance of an object implementing <b>Iterator</b> or
	 * <b>Traversable</b>
	 */
	public function getIterator() {
		return new \ArrayIterator($this->children);
	}
} 