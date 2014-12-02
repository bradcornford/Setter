<?php namespace Cornford\Setter;

use Cornford\Setter\Contracts\SettableInterface;
use Cornford\Setter\Exceptions\SettingVariableException;

class Setting extends SettingBase implements SettableInterface {

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

		$this->cache
			->forget($this->attachTag($key));
		$this->cache
			->add($this->attachTag($key), $value, $this->expiry);

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
		if ($this->cacheHas($key)) {
			return $this->returnCache($key);
		}

		$results = $this->database
			->table('settings')
			->where('settings.key', '=', $key)
			->whereRaw('settings.key LIKE "' . $key . '.%"', array(), 'or')
			->lists('value', 'key');

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

		$this->cache
			->forget($this->attachTag($key));

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
		if ($this->cacheHas($this->attachTag($key))) {
			$result = true;
		} else {
			$result = $this->database
				->table('settings')
				->select('settings.value')
				->where('settings.key', '=', $key)
				->count();
		}

		return $result ? true : false;
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

		$this->cache
			->flush();

		return $result ? true : false;
	}

	/**
	 * Set the cache expiry
	 *
	 * @param boolean|integer|datetime $expiry
	 *
	 * @throws SettingVariableException
	 *
	 * @return self
	 */
	public function expires($expiry)
	{
		if (!is_bool($expiry) && !is_integer($expiry) && !$expiry instanceof \DateTime) {
			throw new SettingVariableException('Invalid expiry value.');
		}

		$this->expiry = $expiry;

		return $this;
	}
}
