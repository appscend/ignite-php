<?php

namespace Ignite;


class ViewStub {

	private $properties = [];

	public function __construct($id, $type, $r) {
		$this->properties['id'] = $id;
		$this->properties['type'] = $type;
		$this->properties['route'] = $r;
	}

	public function __get($k) {
		if (isset($this->properties[$k]))
			return $this->properties[$k];

		return null;
	}

} 