<?php namespace Cornford\Setter;

use Cornford\Setter\Exceptions\SettingArgumentException;
use DateTime;
use Illuminate\Database\DatabaseManager as Query;
use Illuminate\Config\Repository;
use Illuminate\Cache\Repository as Cache;

abstract class SettingBase {

	const LOCATION_DATABASE = 'database';
	const LOCATION_CACHE = 'cache';

	const CACHE_ENABLED = true;
	const CACHE_TAG = 'setter::';
	const CACHE_EXPIRY = true;

	/**
	 * Database
	 *
	 * @var \Illuminate\Database\DatabaseManager
	 */
	protected $databaseInstance;

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
	 * Caching Enabled?
	 *
	 * @var boolean
	 */
	protected $cacheEnabled = true;

	/**
	 * Cache Tag
	 *
	 * @var string
	 */
	protected $cacheTag;

	/**
	 * Cache
	 *
	 * @var integer|datetime|boolean
	 */
	protected $cacheExpiry;

	/**
	 * Un-cached?
	 *
	 * @var boolean
	 */
	protected $uncached = false;

	/**
	 * Construct Setter
	 *
	 * @param Query      $database
	 * @param Repository $config
	 * @param Cache      $cache
	 * @param array      $options
	 *
	 * @throws SettingArgumentException
	 */
	public function __construct(Query $database, Repository $config, Cache $cache, array $options = [])
	{
		$this->database = $database;
		$this->config = $config;
		$this->cache = $cache;

		if (!isset($options['cache'])) {
			throw new SettingArgumentException('Cache is required in boolean format.');
		}
		
		if (!isset($options['tag'])) {
			throw new SettingArgumentException('Tag is required in string format.');
		}

		if (!isset($options['expiry'])) {
			throw new SettingArgumentException('Expiry is required in boolean, integer or DateTime format.');
		}

		$this->setCacheEnabled(isset($options['cache']) ? $options['cache'] : self::CACHE_ENABLED);
		$this->setCacheTag(isset($options['tag']) ? $options['tag'] : self::CACHE_TAG);
		$this->setCacheExpiry(isset($options['expiry']) ? $options['expiry'] : self::CACHE_EXPIRY);
	}

	/**
	 * Set caching enabled status.
	 *
	 * @param boolean $value
	 *
	 * @throws SettingArgumentException
	 *
	 * @return void
	 */
	protected function setCacheEnabled($value)
	{
		if (!is_bool($value)) {
			throw new SettingArgumentException('Cache enabled is required in boolean format.');
		}

		$this->cacheEnabled = $value;
	}

	/**
	 * Get the caching enabled status.
	 *
	 * @return boolean
	 */
	protected function getCacheEnabled()
	{
		return $this->cacheEnabled;
	}

	/**
	 * Cache enabled?
	 *
	 * @return boolean
	 */
	public function cacheEnabled()
	{
		return ($this->getCacheEnabled() === self::CACHE_ENABLED);
	}

	/**
	 * Set the cache tag
	 *
	 * @param string $value
	 *
	 * @throws SettingArgumentException
	 *
	 * @return void
	 */
	public function setCacheTag($value)
	{
		if (!is_string($value)) {
			throw new SettingArgumentException('Cache tag is required in string format.');
		}

		$this->cacheTag = $value;
	}

	/**
	 * Get the cache tag
	 *
	 * @return string
	 */
	public function getCacheTag()
	{
		return $this->cacheTag;
	}

	/**
	 * Set the cache expiry
	 *
	 * @param boolean|integer|DateTime $value
	 *
	 * @throws SettingArgumentException
	 *
	 * @return void
	 */
	protected function setCacheExpiry($value)
	{
		if (!is_bool($value) && !is_integer($value) && !$value instanceof DateTime) {
			throw new SettingArgumentException('Expiry is required in boolean, integer or DateTime format.');
		}

		$this->cacheExpiry = $value;
	}

	/**
	 * Get the cache tag
	 *
	 * @return string
	 */
	protected function getCacheExpiry()
	{
		return $this->cacheExpiry;
	}

	/**
	 * Set the uncached status.
	 *
	 * @param boolean $value
	 *
	 * @return void
	 */
	protected function setUncached($value)
	{
		$this->uncached = $value;
	}

	/**
	 * Get the uncached status.
	 *
	 * @return boolean
	 */
	protected function getUncached()
	{
		return $this->uncached;
	}

	/**
	 * Return a key with an attached cache tag
	 *
	 * @param string $key
	 *
	 * @return string
	 */
	protected function attachCacheTag($key)
	{
		return $this->getCacheTag() . $key;
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
		return $this->cache->has($this->attachCacheTag($key)) ? true : false;
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
			->forget($this->attachCacheTag($key));

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
			$parts = strpos($path, '.') > 0 ? explode('.', trim(preg_replace('/^' . $key . '/', '', $path), '.')) : array($path);
			$target =& $return;

			foreach ($parts as $part) {
				$target =& $target[$part];
			}

			$target = $this->decodeJson($value);
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
	protected function returnResults($results = array(), $key)
	{
		$items = $this->arrangeResults($results, $key);
		$return = $this->combineResults($items, $key);

		if ((!is_array($this->returnConfig($key)) || count($this->returnConfig($key)) == 0) &&
			(array_key_exists($key, $return) || array_key_exists('', $return))
			&& count($return) == 1
		) {
			$return = reset($return);
		}

		if ($this->cacheEnabled()) {
			$this->cache->forget($this->attachCacheTag($key));
			$this->cache->add($this->attachCacheTag($key), $return, $this->getCacheExpiry());
		}

		return $this->decodeJson($return);
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
			return array_replace_recursive($config, ((array_key_exists($key, $results) || array_key_exists('', $results)) ? reset($results) : $results));
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
				->forget($this->attachCacheTag(rtrim(substr_replace($key, '', $position), '.')));
		}

		$this->cache
			->forget($this->attachCacheTag($key));
		$this->cache
			->add($this->attachCacheTag($key), $value, $this->getCacheExpiry());
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
		$value = $this->cache->get($this->attachCacheTag($key));

		return $this->decodeJson($value);
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

		return $this->decodeJson($value);
	}

	/**
	 * Is the string Json encoded.
	 *
	 * @param string $string
	 * @return boolean
	 */
	protected function isJson($string)
	{
		if (!is_string($string)) {
			return false;
		}

		json_decode($string);

		return (json_last_error() == JSON_ERROR_NONE);
	}

	/**
	 * Decode a Json item.
	 *
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	protected function decodeJson($value)
	{
		if ($this->isJson($value)) {
			return ($value === '""' || $value === '' ? '' : json_decode($value));
		}

		return $value;
	}

}
