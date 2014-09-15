<?php

namespace Ignite;


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

} 