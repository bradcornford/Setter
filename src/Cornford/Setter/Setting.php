<?php namespace Cornford\Setter;

use Cornford\Setter\Contracts\CacheableInterface;
use Cornford\Setter\Contracts\SettableInterface;
use Cornford\Setter\Exceptions\SettingArgumentException;
use DateTime;

class Setting extends SettingBase implements SettableInterface, CacheableInterface {

	/**
	 * Set a setting by key and value
	 *
	 * @param string  $key
	 * @param string  $value
	 *
	 * @return boolean
	 */
	public function set($key, $value)
	{
		$query = $this->database
			->table('settings');
		$value = json_encode($value);

		if ($this->has($key)) {
			$result = $query->where('key', $key)
				->update(array('value' => $value));
		} else {
			$result = $query->insert(array('key' => $key, 'value' => $value));
		}

		if ($this->cacheEnabled()) {
			$this->recacheItem($value, $key);
		}

		return $result ? true : false;
	}

	/**
	 * Get a setting by key, optionally set a default or fallback to config lookup
	 *
	 * @param string  $key
	 * @param string  $default
	 *
	 * @return string|array|boolean
	 */
	public function get($key, $default = null)
	{
		if (!$this->getUncached() && $this->cacheEnabled() && $this->cacheHas($this->attachCacheTag($key))) {
			return $this->returnCache($key);
		}

		$results = $this->database
			->table('settings')
			->where('settings.key', '=', $key)
			->whereRaw('settings.key LIKE "' . $key . '.%"', array(), 'or')
			->lists('value', 'key');

		$this->setUncached(false);

		if ($results) {
			return $this->returnResults($results, $key);
		}
		
		if ($default) {
			return $default;
		}

		if ($this->configHas($key)) {
			return $this->returnConfig($key);
		}

		return false;
	}

	/**
	 * Forget a setting by key
	 *
	 * @param string $key
	 *
	 * @return boolean
	 */
	public function forget($key)
	{
		$result = $this->database
			->table('settings')
			->where('key', '=', $key)
			->delete();

		if ($this->cacheEnabled() && $this->cacheHas($this->attachCacheTag($key))) {
			$this->cache
				->forget($this->attachCacheTag($key));
		}

		return $result ? true : false;
	}

	/**
	 * Check a setting exists by key
	 *
	 * @param string $key
	 *
	 * @return boolean
	 */
	public function has($key)
	{
		if ($this->cacheEnabled() && $this->cacheHas($this->attachCacheTag($key))) {
			$result = true;
		} else {
			$result = $this->database
				->table('settings')
				->select('settings.value')
				->where('settings.key', '=', $key)
				->count();
		}

		return ($result ? true : false);
	}

	/**
	 * Get all stored settings
	 *
	 * @return array
	 */
	public function all()
	{
		$results = $this->database
			->table('settings')
			->lists('value', 'key');

		return $this->arrangeResults($results);
	}

	/**
	 * Clear all stored settings
	 *
	 * @return boolean
	 */
	public function clear()
	{
		$result = $this->database
			->table('settings')
			->truncate();

		if ($this->cacheEnabled()) {
			$this->cacheClear();			
		}

		return $result ? true : false;
	}

	/**
	 * Set the expiry
	 *
	 * @param boolean|integer|Datetime $expiry
	 *
	 * @throws SettingArgumentException
	 *
	 * @return self
	 */
	public function expires($expiry)
	{
		$this->cacheExpires($expiry);

		return $this;
	}

	/**
	 * Enable caching.
	 *
	 * @return self
	 */
	public function enableCache()
	{
		$this->setCacheEnabled(true);

		return $this;
	}

	/**
	 * Disable caching.
	 *
	 * @return self
	 */
	public function disableCache()
	{
		$this->setCacheEnabled(false);

		return $this;
	}

	/**
	 * Sets the uncached flag to request an item from the DB and re-cache the item.
	 *
	 * @return self
	 */
	public function uncached()
	{
		$this->setUncached(true);

		return $this;
	}

	/**
	 * Set the cache expiry
	 *
	 * @param boolean|integer|Datetime $expiry
	 *
	 * @throws SettingArgumentException
	 *
	 * @return self
	 */
	public function cacheExpires($expiry)
	{
		if (!is_bool($expiry) && !is_integer($expiry) && !$expiry instanceof DateTime) {
			throw new SettingArgumentException('Expiry is required in boolean, integer or DateTime format.');
		}

		$this->cacheExpiry = $expiry;

		return $this;
	}

}
