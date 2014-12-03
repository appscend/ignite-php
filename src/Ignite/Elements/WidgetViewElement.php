<?php
namespace Ignite\Elements;

use Ignite\Element;
use Ignite\Action;
use Ignite\Actions\ActionBuffer;

class WidgetViewElement extends Element {

	/**
	 * @var Action
	 */
	private $swipeLeftAction = null;

	/**
	 * @var Action
	 */
	private $swipeRightAction = null;

	/**
	 * @param \Closure|Action $action
	 * @param string $name
	 * @throws \InvalidArgumentException
	 */
	public function onSwipeLeft($action, $name = null) {
		if ($action instanceof \Closure) {
			$action();

			$fresult = ActionBuffer::getBuffer();

			if (!isset($fresult[1])) {
				$ac = $fresult[0];
				$ac->setPrefix('swl');
				ActionBuffer::clearBuffer();
			} else {

				$el = $this->view->addActionGroup($fresult, $name);

				if ($name !== null)
					$ac = new Action('pag:', [$name], 'swl');
				else {
					$index = $this->view['action_groups']->getChildIndex($el);
					$ac = new Action('pag:', [$index-1], 'swl');
				}
			}
		} else if ($action instanceof Action) {
			$ac = $action;
			$ac->setPrefix('swl');
		} else
			throw new \InvalidArgumentException("Parameter 1 for 'onSwipeLeft' must be instance of Action or Closure.");

		$this->swipeLeftAction = $ac;
	}

	/**
	 * @param \Closure|Action $action
	 * @param string $name
	 * @throws \InvalidArgumentException
	 */
	public function onSwipeRight($action, $name = null) {
		if ($action instanceof \Closure) {
			$action();

			$fresult = ActionBuffer::getBuffer();

			if (!isset($fresult[1])) {
				$ac = $fresult[0];
				$ac->setPrefix('swr');
				ActionBuffer::clearBuffer();
			} else {

				$el = $this->view->addActionGroup($fresult, $name);

				if ($name !== null)
					$ac = new Action('pag:', [$name], 'swr');
				else {
					$index = $this->view['action_groups']->getChildIndex($el);
					$ac = new Action('pag:', [$index-1], 'swr');
				}
			}
		} else if ($action instanceof Action) {
			$ac = $action;
			$ac->setPrefix('swr');
		} else
			throw new \InvalidArgumentException("Parameter 1 for 'onSwipeRight' must be instance of Action or Closure.");

		$this->swipeRightAction = $ac;
	}

	/**
	 * @param bool $update
	 * @return array
	 */
	public function render($update = false) {
		$result = parent::render($update);

		if ($this->swipeLeftAction !== null)
			$result = array_merge($result, $this->swipeLeftAction->render($update));

		if ($this->swipeRightAction !== null)
			$result = array_merge($result, $this->swipeRightAction->render($update));

		return $result;
	}

} 