<?php
/**
 * @link https://github.com/misaret/nomos-phalcon/
 * @copyright Copyright (c) 2014 Vitalii Khranivskyi
 * @author Vitalii Khranivskyi <misaret@gmail.com>
 * @license LICENSE file
 */

namespace misaret\Phalcon;

use Phalcon\Session\Adapter;
use Phalcon\Session\AdapterInterface;
use Phalcon\Session\Exception;
use misaret\nomos\Session;

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
		if (!isset($options['storage']))
			throw new Exception("Parameter 'storage' is required");
		if (!isset($options['level']))
			throw new Exception("Parameter 'level' is required");
		if (!isset($options['subLevel']))
			$options['subLevel'] = 0;

		$handler = new Session($options['storage'], $options['level'], $options['subLevel'], @$options['lifetime']);

		session_set_save_handler($handler);

        parent::__construct($options);
    }
}
