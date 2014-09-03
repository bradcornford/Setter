<?php namespace Cornford\Setter\Contracts;

interface SettableInterface {

	/**
	 * Set a setting by key and value
	 *
	 * @param string  $key
	 * @param string  $value
	 *
	 * @return boolean
	 */
	public function set($key, $value);

	/**
	 * Get a setting by key, optionally set a default or fallback to config lookup
	 *
	 * @param string  $key
	 * @param string  $default
	 *
	 * @return string
	 */
	public function get($key, $default = null);

	/**
	 * Forget a setting by key
	 *
	 * @param string $key
	 *
	 * @return boolean
	 */
	public function forget($key);

	/**
	 * Check a setting exists by key
	 *
	 * @param string $key
	 *
	 * @return boolean
	 */
	public function has($key);

	/**
	 * Get all stored settings
	 *
	 * @return array
	 */
	public function all();

	/**
	 * Clear all stored settings
	 *
	 * @return boolean
	 */
	public function clear();

	/**
	 * Set the cache expiry
	 *
	 * @param boolean|integer|datetime $expiry
	 *
	 * @throws SettingVariableException
	 *
	 * @return self
	 */
	public function expires($expiry);

}
