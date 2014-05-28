<?php namespace Cornford\Setter;

use Illuminate\Database\DatabaseManager as Query;
use Illuminate\Config\Repository;

abstract class SettingBase {

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
	 * Construct Setter
	 *
	 * @param Query      $database
	 * @param Repository $config
	 *
	 * @return self
	 */
	public function __construct(Query $database, Repository $config)
	{
		$this->database = $database;
		$this->config = $config;
	}

}