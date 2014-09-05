<?php namespace Cornford\Setter;

use Illuminate\Database\DatabaseManager as Query;
use Illuminate\Config\Repository;
use Illuminate\Cache\Repository as Cache;

abstract class SettingBase {

	const LOCATION_DATABASE = 'database';
	const LOCATION_CACHE = 'cache';

	const CACHE_TAG = 'setter::';

	/**
	 * Database
	 *
	 * @var \Illuminate\Database\DatabaseManager
	 */
	protected $database;

	/**
	 * Config
	 *
	 * @var \Illuminate\Config\Repository
	 */
	protected $config;

	/**
	 * Cache
	 *
	 * @var \Illuminate\Cache\Repository
	 */
	protected $cache;

	/**
	 * Cache
	 *
	 * @var integer|datetime|boolean
	 */
	protected $expiry = true;

	/**
	 * Construct Setter
	 *
	 * @param Query      $database
	 * @param Repository $config
	 * @param Cache      $cache
	 *
	 * @return self
	 */
	public function __construct(Query $database, Repository $config, Cache $cache)
	{
		$this->database = $database;
		$this->config = $config;
		$this->cache = $cache;
	}

	/**
	 * Return a key with an attached cache tag
	 *
	 * @param string $key
	 *
	 * @return string
	 */
	protected function attachTag($key)
	{
		return self::CACHE_TAG . $key;
	}

	/**
	 * Arrange results into an associative array
	 *
	 * @param array  $results
	 * @param string $key
	 *
	 * @return array
	 */
	protected function arrangeResults($results, $key = null)
	{
		$return = array();
		foreach ($results as $path => $value) {
			$parts = explode('.', trim(preg_replace('/^' . $key . '/', '', $path), '.'));
			$target =& $return;

			foreach ($parts as $part) {
				$target =& $target[$part];
			}

			$target = json_decode($value) ?: $value;
		}

		return $return;
	}

}