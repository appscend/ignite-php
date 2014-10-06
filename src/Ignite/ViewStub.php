<?php

namespace Ignite;


use Ignite\Actions\ActionBuffer;
use Ignite\Actions\ActionGroup;

class ViewStub {

	private $viewTypes = [
		'cam',
		'c',
		'fr',
		'p',
		'lr',
		'l',
		'm',
		'mb',
		'cs',
		't',
		'w',
		'wd',
	];

	private $properties = [];

	public function __construct($id, $type, $r) {
		$this->properties['id'] = $id;
		$this->properties['type'] = $this->viewTypes[$type];
		$this->properties['route'] = $r;
	}

	public function __get($k) {
		if (isset($this->properties[$k]))
			return $this->properties[$k];

		return null;
	}

	/**
	 *
	 * These metods are used to generate an action group executed with this view's ID (tavi)
	 *
	 * @param $name
	 * @param $params
	 */
	public function __call($name, $params) {
		$a = ActionGroup::get($name)->on($this->properties['id']);
	}

} 