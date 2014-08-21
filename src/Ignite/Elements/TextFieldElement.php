<?php
namespace Ignite\Elements;

use Ignite\Element;
use Ignite\Action;
use Ignite\Actions\ActionBuffer;

class TextFieldElement extends Element{

	/**
	 * @var Action
	 */
	private $selectionAction = null;

	/**
	 * @param null|string $tag
	 * @param array $properties
	 */
	public function __construct($tag, array $properties = []) {
		parent::__construct($tag, $properties);
		$this['control_type'] = 'tf';
	}

	/**
	 *
	 * Performs an action after selecting text in the field.
	 *
	 * @param \Closure|Action $action Instance of Action or closure which calls static Action class methods
	 * @param string $name Name of the action in case closure ret
	 * @throws \InvalidArgumentException
	 */
	public function onSelection($action, $name = null) {
		if ($action instanceof \Closure) {
			$action();

			$fresult = ActionBuffer::getAndClearBuffer();

			if (!isset($fresult[1])) {
				$ac = $fresult[0];
				$ac->setPrefix('s');
			} else {

				$el = $this->view->addActionGroup($fresult, $name);

				if ($name !== null)
					$ac = new Action('pag:', [$name], 's');
				else {
					$index = $this->view['action_groups']->getChildIndex($el);
					$ac = new Action('pag:', [$index-1], 's');
				}
			}
		} else if ($action instanceof Action) {
			$ac = $action;
			$ac->setPrefix('s');
		} else
			throw new \InvalidArgumentException("Parameter 1 for 'onSelection' must be instance of Action or Closure.");

		$this->selectionAction = $ac;
	}

	/**
	 * @param bool $update
	 * @return array
	 */
	public function render($update = false) {
		$result = parent::render($update);

		if ($this->selectionAction !== null)
			$result = array_merge($result, $this->selectionAction->render($update));

		return $result;
	}

} 