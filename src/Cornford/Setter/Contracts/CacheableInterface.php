<?php namespace Cornford\Setter\Contracts;

use Cornford\Setter\Exceptions\SettingArgumentException;

interface CacheableInterface {

	/**
	 * Cache enabled?
	 *
	 * @return boolean
	 */
	public function cacheEnabled();

	/**
	 * Enable caching.
	 *
	 * @return self
	 */
	public function enableCache();

	/**
	 * Disable caching.
	 *
	 * @return self
	 */
	public function disableCache();

	/**
	 * Set the cache tag
	 *
	 * @param string $value
	 *
	 * @throws SettingArgumentException
	 *
	 * @return void
	 */
	public function setCacheTag($value);

	/**
	 * Get the cache tag
	 *
	 * @return string
	 */
	public function getCacheTag();

	/**
	 * Sets the uncached flag to request an item from the DB and re-cache the item if caching is enabled.
	 *
	 * @return self
	 */
	public function uncached();

	/**
	 * Check a setting exists in cache
	 *
	 * @param string $key
	 *
	 * @return boolean
	 */
	public function cacheHas($key);

	/**
	 * Forget a cached setting by key
	 *
	 * @param string $key
	 *
	 * @return boolean
	 */
	public function cacheForget($key);

	/**
	 * Clear all cached settings
	 *
	 * @return boolean
	 */
	public function cacheClear();

	/**
	 * Set the cache expiry
	 *
	 * @param boolean|integer|Datetime $expiry
	 *
	 * @throws SettingArgumentException
	 *
	 * @return self
	 */
	public function cacheExpires($expiry);

}
