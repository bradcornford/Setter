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
		$query = $this->database
			->table('settings');

		if ($this->has($key)) {
			$result = $query->where('key', $key)
				->update(
					array('value' => json_encode($value))
				);
		} else {
			$result = $query->insert(
				array('key' => $key, 'value' => json_encode($value))
			);
		}

		return $result ? true : false;
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
		$results = $this->database
			->table('settings')
			->where('settings.key', '=', $key)
			->whereRaw('settings.key LIKE "' . $key . '.%"', array(), 'or')
			->lists('value', 'key');

		if ($results) {
			if (count($results) > 1) {
				return $this->arrangeResults($results, $key);
			}

			return json_decode($results[$key]);
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
		$result = $this->database
			->table('settings')
			->select('settings.value')
			->where('settings.key', '=', $key)
			->get();

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

		return $result ? true : false;
	}

	/**
	 * Arrange results into an associative array
	 *
	 * @param array  $results
	 * @param string $key
	 *
	 * @return array
	 */
	private function arrangeResults($results, $key = null)
	{
		$return = array();
		foreach ($results as $path => $value) {
			$parts = explode('.', trim(preg_replace('/^' . $key . '/', '', $path), '.'));
			$target =& $return;

			foreach ($parts as $part) {
				$target =& $target[$part];
			}

			$target = json_decode($value);
		}

		return $return;
	}
}
