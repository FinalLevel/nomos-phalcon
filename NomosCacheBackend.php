<?php
/**
 * @link https://github.com/misaret/nomos-phalcon/
 * @copyright Copyright (c) 2014 Vitalii Khranivskyi
 * @author Vitalii Khranivskyi <misaret@gmail.com>
 * @license LICENSE file
 */

namespace misaret\Phalcon;

use misaret\nomos\Storage;
use Phalcon\Cache\Backend;
use Phalcon\Cache\BackendInterface;
use Phalcon\Cache\FrontendInterface;
use Phalcon\Cache\Exception;

/**
 * Nomos extends [[Cache]] by using Nomos Storage as cache engine.
 */
class NomosCacheBackend extends Backend implements BackendInterface
{
	/**
	 * @var \misaret\nomos\Storage
	 */
	private $_storage;

	/**
	 * Class constructor.
	 *
	 * @param \Phalcon\Cache\FrontendInterface $frontend
	 * @param array $options
	 * @throws \Phalcon\Cache\Exception
	 */
	public function __construct(FrontendInterface $frontend, array $options)
	{
		if (!isset($options['storage']))
			throw new Exception("Parameter 'storage' is required");
		$this->_storage = $options['storage'];
		if (!isset($options['level']))
			throw new Exception("Parameter 'level' is required");
		if (!isset($options['subLevel']))
			$options['subLevel'] = 0;

		parent::__construct($frontend, $options);
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param string $keyName
	 * @param integer $lifetime
	 * @return mixed
	 */
	public function get($keyName, $lifetime = null)
	{
		$options = $this->getOptions();
		$key = Storage::buildKey($keyName);

		$value = $this->_storage->get($options['level'], $options['subLevel'], $key, (int) $lifetime);
		if ($value === false)
			return null;

		$frontend = $this->getFrontend();

		$this->setLastKey($key);

		return $frontend->afterRetrieve($value);
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param string $keyName
	 * @param string $content
	 * @param integer $lifetime
	 * @param boolean $stopBuffer
	 * @throws \Phalcon\Cache\Exception
	 */
	public function save($keyName = null, $content = null, $lifetime = null, $stopBuffer = true)
	{
		if ($keyName === null) {
			$lastKey = $this->_lastKey;
		} else {
			$lastKey = $keyName;
		}
		if (!$lastKey) {
			throw new Exception('The cache must be started first');
		}

		$frontend = $this->getFrontend();

		if ($content === null) {
			$content = $frontend->getContent();
		}

		// Get the lifetime from the frontend
		if ($lifetime === null) {
			$lifetime = $frontend->getLifetime();
		}

		$options = $this->getOptions();
		$this->_storage->put($options['level'], $options['subLevel'], $lastKey, $lifetime, $frontend->beforeStore($content));

		$isBuffering = $frontend->isBuffering();

		// Stop the buffer, this only applies for Phalcon\Cache\Frontend\Output
		if ($stopBuffer) {
			$frontend->stop();
		}

		// Print the buffer, this only applies for Phalcon\Cache\Frontend\Output
		if ($isBuffering) {
			echo $content;
		}

		$this->_started = false;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param string $keyName
	 * @return boolean
	 */
	public function delete($keyName)
	{
		$options = $this->getOptions();

		return $this->_storage->delete($options['level'], $options['subLevel'], $keyName);
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param string $prefix
	 * @return array
	 */
	public function queryKeys($prefix = null)
	{
		return [];
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param string $keyName
	 * @param string $lifetime
	 * @return boolean
	 */
	public function exists($keyName = null, $lifetime = null)
	{
		$options = $this->getOptions();

		return $this->_storage->get($options['level'], $options['subLevel'], $keyName);
	}
}
