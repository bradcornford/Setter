<?php namespace Cornford\Setter;

use Cornford\Setter\Contracts\SettableInterface;

class Setting extends SettingBase implements SettableInterface {

	/**
	 * Set a setting by key and value
	 *
	 * @param string $key
	 * @param string $value
	 *
	 * @return boolean
	 */
	public function set($key, $value)
	{
		$result = $this->database
			->table('settings')->insert(
				array('key' => $key, 'value' => $value)
			);

		if (!$result) {
			return false;
		}

		return true;
	}

	/**
	 * Get a setting by key, optionally set a default or fallback to config lookup
	 *
	 * @param string $key
	 * @param string $default
	 *
	 * @return string
	 */
	public function get($key, $default = null)
	{
		$result = $this->database
			->table('settings')
			->select('settings.value')
			->where('settings.key', '=', $key)
			->first();

		if ($result) {
			return $result->value;
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

		if (!$result) {
			return false;
		}

		return true;
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
		$result = $this->database
			->table('settings')
			->select('settings.value')
			->where('settings.key', '=', $key)
			->first();

		return $result ?: false;
	}

	/**
	 * Get all stored settings
	 *
	 * @return array
	 */
	public function all()
	{
		$result = $this->database
			->table('settings')
			->select('settings.key', 'settings.value')
			->get();

		return json_decode(json_encode($result), true);
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

		return $result ?: false;
	}

}
