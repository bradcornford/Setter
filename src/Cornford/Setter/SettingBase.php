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
	 * Forget a cached setting by key
	 *
	 * @param string $key
	 *
	 * @return boolean
	 */
	public function cacheForget($key)
	{
		$this->cache
			->forget($this->attachTag($key));

		return true;
	}

	/**
	 * Clear all cached settings
	 *
	 * @return boolean
	 */
	public function cacheClear()
	{
		$this->cache
			->flush();

		return true;
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

			if ($value === '""') {
				$value = '';
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
	protected function returnResults(array $results = array(), $key)
	{
		$return = array();

		if ($results) {
			$items = $this->arrangeResults($results, $key);
			$return = $this->combineResults($items, $key);
		}

		if (count($return) == 1) {
			$firstResult = reset($results);
			$return = $firstResult == '""' ? '' : $firstResult;
			$return = @json_decode($return) ?: $return;
		}

		$this->cache->forget($this->attachTag($key));
		$this->cache->add($this->attachTag($key), $return, $this->expiry);

		return $return;
	}

	/**
	 * Combine result values from the database and configuration
	 *
	 * @param array  $results
	 * @param string $key
	 *
	 * @return array
	 */
	protected function combineResults(array $results = array(), $key)
	{
		$config = $this->returnConfig($key);

		if (is_array($config)) {
			return array_replace_recursive($config, $results);
		}

		return $results;
	}

	/**
	 * Re-cache item and its parents
	 *
	 * @param string $value
	 * @param string $key
	 *
	 * @return void
	 */
	protected function recacheItem($value, $key)
	{
		for ($i = 0; $i <= substr_count($key, '.') - 1; $i++) {
			$j = $i;
			$position = 0;

			while ($j >= 0) {
				$position =+ strpos($key, '.', $position) + 1;
				$j--;
			}

			$this->cache
				->forget($this->attachTag(rtrim(substr_replace($key, '', $position), '.')));
		}

		$this->cache
			->forget($this->attachTag($key));
		$this->cache
			->add($this->attachTag($key), $value, $this->expiry);
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
		$value = $this->cache->get($this->attachTag($key));

		if ($value === '""') {
			$value = '';
		}

		return @json_decode($value === '""' ? '' : $value) ?: $value;
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
		$value = $this->config->get($key);

		if ($value === '""') {
			$value = '';
		}

		return @json_decode($value) ?: $value;
	}

}