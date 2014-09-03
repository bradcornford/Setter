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

		$this->cache->add($this->attachTag($key), $value, $this->expiry);

		return $result ? true : false;
	}

	/**
	 * Get a setting by key, optionally set a default or fallback to config lookup
	 *
	 * @param string  $key
	 * @param string  $default
	 *
	 * @return string
	 */
	public function get($key, $default = null)
	{
		if ($this->cache->has($this->attachTag($key))) {
			return $this->cache->get($this->attachTag($key));
		}

		$results = $this->database
			->table('settings')
			->where('settings.key', '=', $key)
			->whereRaw('settings.key LIKE "' . $key . '.%"', array(), 'or')
			->lists('value', 'key');

		if ($results) {
			if (count($results) > 1) {
				$results = $this->arrangeResults($results, $key);
				$this->cache->add($this->attachTag($key), $results, $this->expiry);

				return $results;
			}

			$this->cache->add($this->attachTag($key), json_decode($results[$key]), $this->expiry);

			return json_decode($results[$key]) ?: $results[$key];
		}

		if ($default) {
			return $default;
		}

		if ($this->config->has($key)) {
			return $this->config->get($key);
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

		$this->cache->forget($this->attachTag($key));

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
		if ($this->cache->has($this->attachTag($key))) {
			$result = $this->cache->get($this->attachTag($key));
		} else {
			$result = $this->database
				->table('settings')
				->select('settings.value')
				->where('settings.key', '=', $key)
				->get();
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

		$this->cache->flush();

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
