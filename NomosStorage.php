<?php
/**
 * @link https://github.com/FinalLevel/nomos-phalcon/
 * @copyright Copyright (c) 2014 Vitalii Khranivskyi
 * @author Vitalii Khranivskyi <misaret@gmail.com>
 * @license LICENSE file
 */

namespace fl\Phalcon;

/**
 * Nomos Storage for Phalcon
 */
class NomosStorage extends \fl\nomos\Storage implements \Phalcon\Events\EventsAwareInterface
{
    protected $_eventsManager;

    public function setEventsManager($eventsManager)
    {
        $this->_eventsManager = $eventsManager;
    }

    public function getEventsManager()
    {
        return $this->_eventsManager;
    }

	protected function _fireEvent($eventName)
	{
		if ($this->_eventsManager) {
			$this->_eventsManager->fire('nomos:' . $eventName, $this);
		}

		return parent::_fireEvent($eventName);
	}
}
