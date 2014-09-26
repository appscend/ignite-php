<?php
namespace Ignite\Elements;

use Ignite\Element;
use Ignite\Action;
use Ignite\Actions\ActionBuffer;

class ListElement extends Element{

	/**
	 * @var Action
	 */
	private $accessoryAction = null;

	/**
	 * @var Action
	 */
	private $selectedItemAction = null;

	/**
	 * @param \Closure|Action $action
	 * @param string $name
	 * @throws \InvalidArgumentException
	 */
	public function onAccessoryTap($action, $name = null) {
		if ($action instanceof \Closure) {
			$action();

			$fresult = ActionBuffer::getBuffer();

			if (!isset($fresult[1])) {
				$ac = $fresult[0];
				$ac->setPrefix('a');
			} else {

				$el = $this->view->addActionGroup($fresult, $name);

				if ($name !== null)
					$ac = new Action('pag:', [$name], 'a');
				else {
					$index = $this->view['action_groups']->getChildIndex($el);
					$ac = new Action('pag:', [$index-1], 'a');
				}
			}
		} else if ($action instanceof Action) {
			$ac = $action;
			$ac->setPrefix('a');
		} else
			throw new \InvalidArgumentException("Parameter 1 for 'onAccessoryTap' must be instance of Action or Closure.");

		$this->accessoryAction = $ac;
	}

	/**
	 * @param \Closure|Action $action
	 * @param string $name
	 * @throws \InvalidArgumentException
	 */
	public function onSelectedItemTap($action, $name = null) {
		if ($action instanceof \Closure) {
			$action();

			$fresult = ActionBuffer::getBuffer();

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
			throw new \InvalidArgumentException("Parameter 1 for 'onSelectedItemTap' must be instance of Action or Closure.");

		$this->selectedItemAction = $ac;
	}

	/**
	 * @param bool $update
	 * @return array
	 */
	public function render($update = false) {
		$result = parent::render($update);

		if ($this->accessoryAction !== null)
			$result = array_merge($result, $this->accessoryAction->render($update));

		if ($this->selectedItemAction !== null)
			$result = array_merge($result, $this->selectedItemAction->render($update));

		return $result;
	}

} 