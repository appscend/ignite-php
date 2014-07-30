<?php
namespace Ignite\Elements;

use Ignite\Element;
use Ignite\Action;
use Ignite\Actions\ActionBuffer;

class WidgetImage extends Element {

	/**
	 * @var Action
	 */
	private $alternateAction = null;

	/**
	 * @var Action
	 */
	private $doubleTapAction = null;

	/**
	 * @var Action
	 */
	private $swipeLeftAction = null;

	/**
	 * @var Action
	 */
	private $swipeRightAction = null;

	public function __construct($tag, array $properties = []) {
		parent::__construct($tag, $properties);
		$this['element_type'] = 'image';
	}

	/**
	 * @param \Closure|Action $action
	 * @param string $name
	 * @throws \InvalidArgumentException
	 */
	public function onAlternateTap($action, $name = null) {
		if ($action instanceof \Closure) {
			$action();

			$fresult = ActionBuffer::getAndClearBuffer();

			if (!isset($fresult[1])) {
				$ac = $fresult[0];
				$ac->setPrefix('a');
			} else {
				$index = $this->view->addActionGroup($fresult, $name);
				if ($name !== null)
					$ac = new Action('pag:', [$name], 'a');
				else
					$ac = new Action('pag:', [$index-1], 'a');
			}
		} else if ($action instanceof Action) {
			$ac = $action;
			$ac->setPrefix('a');
		} else
			throw new \InvalidArgumentException("Parameter 1 for 'onAlternateTap' must be instance of Action or Closure.");

		$this->alternateAction = $ac;
	}

	/**
	 * @param \Closure|Action $action
	 * @param string $name
	 * @throws \InvalidArgumentException
	 */
	public function onDoubleTap($action, $name = null) {
		if ($action instanceof \Closure) {
			$action();

			$fresult = ActionBuffer::getAndClearBuffer();

			if (!isset($fresult[1])) {
				$ac = $fresult[0];
				$ac->setPrefix('d');
			} else {
				$index = $this->view->addActionGroup($fresult, $name);
				if ($name !== null)
					$ac = new Action('pag:', [$name], 'd');
				else
					$ac = new Action('pag:', [$index-1], 'd');
			}
		} else if ($action instanceof Action) {
			$ac = $action;
			$ac->setPrefix('d');
		} else
			throw new \InvalidArgumentException("Parameter 1 for 'onDoubleTap' must be instance of Action or Closure.");

		$this->doubleTapAction = $ac;
	}

	/**
	 * @param \Closure|Action $action
	 * @param string $name
	 * @throws \InvalidArgumentException
	 */
	public function onSwipeLeft($action, $name = null) {
		if ($action instanceof \Closure) {
			$action();

			$fresult = ActionBuffer::getAndClearBuffer();

			if (!isset($fresult[1])) {
				$ac = $fresult[0];
				$ac->setPrefix('swl');
			} else {
				$index = $this->view->addActionGroup($fresult, $name);
				if ($name !== null)
					$ac = new Action('pag:', [$name], 'swl');
				else
					$ac = new Action('pag:', [$index-1], 'swl');
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

			$fresult = ActionBuffer::getAndClearBuffer();

			if (!isset($fresult[1])) {
				$ac = $fresult[0];
				$ac->setPrefix('swr');
			} else {
				$index = $this->view->addActionGroup($fresult, $name);
				if ($name !== null)
					$ac = new Action('pag:', [$name], 'swr');
				else
					$ac = new Action('pag:', [$index-1], 'swr');
			}
		} else if ($action instanceof Action) {
			$ac = $action;
			$ac->setPrefix('swr');
		} else
			throw new \InvalidArgumentException("Parameter 1 for 'onSwipeRight' must be instance of Action or Closure.");

		$this->swipeRightAction = $ac;
	}

	public function render($update = false) {
		$result = parent::render($update);

		if ($this->alternateAction !== null)
			$result = array_merge($result, $this->alternateAction->render($update));

		if ($this->doubleTapAction !== null)
			$result = array_merge($result, $this->doubleTapAction->render($update));

		if ($this->swipeLeftAction !== null)
			$result = array_merge($result, $this->swipeLeftAction->render($update));

		if ($this->swipeRightAction !== null)
			$result = array_merge($result, $this->swipeRightAction->render($update));

		return $result;
	}

} 