<?php
/**
 * @link https://github.com/FinalLevel/nomos-phalcon/
 * @copyright Copyright (c) 2014 Vitalii Khranivskyi
 * @author Vitalii Khranivskyi <misaret@gmail.com>
 * @license LICENSE file
 */

namespace fl\Phalcon;

use Phalcon\Session\Adapter;
use Phalcon\Session\AdapterInterface;
use Phalcon\Session\Exception;

/**
 * Nomos Storage adapter for Phalcon\Session
 */
class NomosSession extends Adapter implements AdapterInterface
{
    /**
     * @param array $options
     * @throws \Phalcon\Session\Exception
     */
    public function __construct($options = null)
    {
		if (!isset($options['storage'])) {
			throw new Exception("Parameter 'storage' is required");
		}
		if (!isset($options['level'])) {
			throw new Exception("Parameter 'level' is required");
		}
		if (!isset($options['subLevel'])) {
			$options['subLevel'] = 0;
		}
		if (empty($options['lifetime'])) {
			$options['lifetime'] = null;
		}

		$handler = new \fl\nomos\Session($options['storage'], $options['level'], $options['subLevel'], $options['lifetime']);

		session_set_save_handler($handler);

        parent::__construct($options);
    }
}
