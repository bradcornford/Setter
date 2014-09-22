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
	 * Check a setting exists in cache
	 *
	 * @param string $key
	 *
	 * @return boolean
	 */
	public function cacheHas($key)
	{
		return $this->cache->has($this->attachTag($key)) ? true : false;
	}

	/**
	 * Check a setting exists in config
	 *
	 * @param string $key
	 *
	 * @return boolean
	 */
	public function configHas($key)
	{
		return $this->config->has($key) ? true : false;
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

			$target = @json_decode($value) ?: $value;
		}

		return $return;
	}

	/**
	 * Return result values
	 *
	 * @param array  $results
	 * @param string $key
	 *
	 * @return string|array
	 */
	protected function returnResults($results, $key)
	{
		if ($results && count($results) > 1) {
			$results = $this->arrangeResults($results, $key);
            $this->cache->forget($this->attachTag($key));
			$this->cache->add($this->attachTag($key), $results, $this->expiry);

			return $results;
		}

		if ($results) {
			$result = @json_decode($results[$key]) ?: $results[$key];
            $this->cache->forget($this->attachTag($key));
			$this->cache->add($this->attachTag($key), $result, $this->expiry);

			return $result;
		}
	}

	/**
	 * Return cache values
	 *
	 * @param string $key
	 *
	 * @return string|array
	 */
	protected function returnCache($key)
	{
		return $this->cache->get($this->attachTag($key));
	}

	/**
	 * Return config values
	 *
	 * @param string $key
	 *
	 * @return string|array
	 */
	protected function returnConfig($key)
	{
		return $this->config->get($key);
	}

}