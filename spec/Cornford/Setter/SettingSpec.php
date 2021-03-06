<?php namespace spec\Cornford\Setter;

use Cornford\Setter\Exceptions\SettingArgumentException;
use Cornford\Setter\Setting;
use Illuminate\Config\Repository as Config;
use Illuminate\Cache\Repository as Cache;
use Illuminate\Database\DatabaseManager;
use PhpSpec\ObjectBehavior;
use Mockery;
use stdClass;

class SettingSpec extends ObjectBehavior
{
	const TAG = 'test::';
	const KEY = 'test';
	const SUB_KEY = 'test.item';
	const SUB_KEY_ITEM_1 = 'test.item.value1';
	const VALUE_1 = 'value1';
	const VALUE_2 = 'value2';
	const STRING = 'value';
	const INTEGER = 1;
	const BOOLEAN = true;

	function it_is_initializable()
	{
		$query = Mockery::mock(DatabaseManager::class);
		$repository = Mockery::mock(Config::class);
		$cache = Mockery::mock(Cache::class);
		$this->beConstructedWith($query, $repository, $cache, ['cache' => true, 'tag' => self::TAG, 'expiry' => true]);

		$this->shouldHaveType(Setting::class);
	}

	function it_throws_an_exception_when_constructed_with_an_invalid_cache_option()
	{
		$query = Mockery::mock(DatabaseManager::class);
		$repository = Mockery::mock(Config::class);
		$cache = Mockery::mock(Cache::class);

		$this->shouldThrow(SettingArgumentException::class)
			->during('__construct', [$query, $repository, $cache, ['cache' => self::STRING, 'tag' => self::TAG, 'expiry' => true]]);
	}

	function it_throws_an_exception_when_constructed_with_an_invalid_tag_option()
	{
		$query = Mockery::mock(DatabaseManager::class);
		$repository = Mockery::mock(Config::class);
		$cache = Mockery::mock(Cache::class);

		$this->shouldThrow(SettingArgumentException::class)
			->during('__construct', [$query, $repository, $cache, ['cache' => true, 'tag' => self::BOOLEAN, 'expiry' => true]]);
	}

	function it_throws_an_exception_when_constructed_with_an_invalid_expiry_option()
	{
		$query = Mockery::mock(DatabaseManager::class);
		$repository = Mockery::mock(Config::class);
		$cache = Mockery::mock(Cache::class);

		$this->shouldThrow(SettingArgumentException::class)
			->during('__construct', [$query, $repository, $cache, ['cache' => true, 'tag' => self::TAG, 'expiry' => self::STRING]]);
	}

	function it_can_set_a_setting()
	{
		$query = Mockery::mock(DatabaseManager::class);
		$query->shouldReceive('table')->andReturn($query);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('count')->andReturn(0);
		$query->shouldReceive('insert')->andReturn(true);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('get')->andReturn(false);

		$repository = Mockery::mock(Config::class);

		$cache = Mockery::mock(Cache::class);

		$this->beConstructedWith($query, $repository, $cache, ['cache' => false, 'tag' => self::TAG, 'expiry' => true]);

		$this->set(self::KEY, self::STRING)->shouldReturn(true);
	}

	function it_can_set_a_setting_with_caching()
	{
		$query = Mockery::mock(DatabaseManager::class);
		$query->shouldReceive('table')->andReturn($query);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('count')->andReturn(0);
		$query->shouldReceive('insert')->andReturn(true);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('get')->andReturn(false);

		$repository = Mockery::mock(Config::class);

		$cache = Mockery::mock(Cache::class);
		$cache->shouldReceive('add')->andReturn(true);
		$cache->shouldReceive('has')->andReturn(false);
		$cache->shouldReceive('forget')->andReturn(true);

		$this->beConstructedWith($query, $repository, $cache, ['cache' => true, 'tag' => self::TAG, 'expiry' => true]);

		$this->set(self::KEY, self::STRING)->shouldReturn(true);
	}

	function it_can_get_a_setting()
	{
		$query = Mockery::mock(DatabaseManager::class);
		$query->shouldReceive('table')->andReturn($query);
		$query->shouldReceive('insert')->andReturn(true);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('count')->andReturn(0);
		$query->shouldReceive('whereRaw')->andReturn($query);
		$query->shouldReceive('lists')->andReturn(array(self::KEY => json_encode(self::STRING)));
		$query->shouldReceive('get')->andReturn(false);

		$repository = Mockery::mock(Config::class);
		$repository->shouldReceive('get')->andReturn(false);

		$cache = Mockery::mock(Cache::class);

		$this->beConstructedWith($query, $repository, $cache, ['cache' => false, 'tag' => self::TAG, 'expiry' => true]);

		$this->set(self::KEY, self::STRING)->shouldReturn(true);
		$this->get(self::KEY)->shouldReturn(self::STRING);
	}

	function it_can_get_a_setting_with_caching()
	{
		$query = Mockery::mock(DatabaseManager::class);
		$query->shouldReceive('table')->andReturn($query);
		$query->shouldReceive('insert')->andReturn(true);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('count')->andReturn(0);
		$query->shouldReceive('whereRaw')->andReturn($query);
		$query->shouldReceive('lists')->andReturn(array(self::KEY => json_encode(self::STRING)));
		$query->shouldReceive('get')->andReturn(false);
		$query->shouldReceive('update')->andReturn(true);

		$repository = Mockery::mock(Config::class);
		$repository->shouldReceive('get')->andReturn(false);

		$cache = Mockery::mock(Cache::class);
		$cache->shouldReceive('add')->andReturn(true);
		$cache->shouldReceive('has')->andReturn(true);
		$cache->shouldReceive('forget')->andReturn(true);
		$cache->shouldReceive('get')->andReturn(self::STRING);

		$this->beConstructedWith($query, $repository, $cache, ['cache' => true, 'tag' => self::TAG, 'expiry' => true]);

		$this->set(self::KEY, self::STRING)->shouldReturn(true);
		$this->get(self::KEY)->shouldReturn(self::STRING);
	}

	function it_can_set_an_already_set_setting()
	{
		$query = Mockery::mock(DatabaseManager::class);
		$query->shouldReceive('table')->andReturn($query);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('count')->andReturn(1);
		$query->shouldReceive('update')->andReturn(true);

		$repository = Mockery::mock(Config::class);

		$cache = Mockery::mock(Cache::class);

		$this->beConstructedWith($query, $repository, $cache, ['cache' => false, 'tag' => self::TAG, 'expiry' => true]);

		$this->set(self::KEY, self::STRING)->shouldReturn(true);
	}

	function it_can_set_an_already_set_setting_with_no_update()
	{
		$query = Mockery::mock(DatabaseManager::class);
		$query->shouldReceive('table')->andReturn($query);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('count')->andReturn(1);
		$query->shouldReceive('update')->andReturn(0);

		$repository = Mockery::mock(Config::class);

		$cache = Mockery::mock(Cache::class);

		$this->beConstructedWith($query, $repository, $cache, ['cache' => false, 'tag' => self::TAG, 'expiry' => true]);

		$this->set(self::KEY, self::STRING)->shouldReturn(true);
	}

	function it_can_set_an_already_set_setting_with_caching()
	{
		$query = Mockery::mock(DatabaseManager::class);
		$query->shouldReceive('table')->andReturn($query);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('count')->andReturn(1);
		$query->shouldReceive('update')->andReturn(true);

		$repository = Mockery::mock(Config::class);

		$cache = Mockery::mock(Cache::class);
		$cache->shouldReceive('add')->andReturn(true);
		$cache->shouldReceive('has')->andReturn(true);
		$cache->shouldReceive('forget')->andReturn(true);

		$this->beConstructedWith($query, $repository, $cache, ['cache' => true, 'tag' => self::TAG, 'expiry' => true]);

		$this->set(self::KEY, self::STRING)->shouldReturn(true);
	}

	function it_can_get_a_setting_with_a_default_value()
	{
		$query = Mockery::mock(DatabaseManager::class);
		$query->shouldReceive('table')->andReturn($query);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('whereRaw')->andReturn($query);
		$query->shouldReceive('get')->andReturn(false);
		$query->shouldReceive('lists')->andReturn(false);

		$repository = Mockery::mock(Config::class);
        $repository->shouldReceive('has')->andReturn(false);

		$cache = Mockery::mock(Cache::class);

		$this->beConstructedWith($query, $repository, $cache, ['cache' => false, 'tag' => self::TAG, 'expiry' => true]);

		$this->get(self::KEY, self::STRING)->shouldReturn(self::STRING);
	}

	function it_can_get_a_setting_with_a_default_value_of_false()
	{
		$query = Mockery::mock(DatabaseManager::class);
		$query->shouldReceive('table')->andReturn($query);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('whereRaw')->andReturn($query);
		$query->shouldReceive('get')->andReturn(false);
		$query->shouldReceive('lists')->andReturn(false);

		$repository = Mockery::mock(Config::class);
        $repository->shouldReceive('has')->andReturn(false);

		$cache = Mockery::mock(Cache::class);

		$this->beConstructedWith($query, $repository, $cache, ['cache' => false, 'tag' => self::TAG, 'expiry' => true]);

		$this->get(self::KEY, false)->shouldReturn(false);
	}

	function it_can_get_a_setting_with_a_default_value_of_an_empty_array()
	{
		$query = Mockery::mock(DatabaseManager::class);
		$query->shouldReceive('table')->andReturn($query);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('whereRaw')->andReturn($query);
		$query->shouldReceive('get')->andReturn(false);
		$query->shouldReceive('lists')->andReturn(false);

		$repository = Mockery::mock(Config::class);
        $repository->shouldReceive('has')->andReturn(false);

		$cache = Mockery::mock(Cache::class);

		$this->beConstructedWith($query, $repository, $cache, ['cache' => false, 'tag' => self::TAG, 'expiry' => true]);

		$this->get(self::KEY, [])->shouldReturn([]);
	}

	function it_can_get_a_setting_with_a_default_value_with_caching()
	{
		$query = Mockery::mock(DatabaseManager::class);
		$query->shouldReceive('table')->andReturn($query);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('whereRaw')->andReturn($query);
		$query->shouldReceive('get')->andReturn(false);
		$query->shouldReceive('lists')->andReturn(false);

		$repository = Mockery::mock(Config::class);

		$cache = Mockery::mock(Cache::class);
		$cache->shouldReceive('add')->andReturn(true);
		$cache->shouldReceive('has')->andReturn(true);
		$cache->shouldReceive('get')->andReturn(self::STRING);

		$this->beConstructedWith($query, $repository, $cache, ['cache' => true, 'tag' => self::TAG, 'expiry' => true]);

		$this->get(self::KEY, self::STRING)->shouldReturn(self::STRING);
	}

	function it_can_get_a_setting_from_a_config_value()
	{
		$query = Mockery::mock(DatabaseManager::class);
		$query->shouldReceive('table')->andReturn($query);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('whereRaw')->andReturn($query);
		$query->shouldReceive('lists')->andReturn(false);

		$repository = Mockery::mock(Config::class);
		$repository->shouldReceive('has')->andReturn(true);
		$repository->shouldReceive('get')->andReturn(self::STRING);

		$cache = Mockery::mock(Cache::class);

		$this->beConstructedWith($query, $repository, $cache, ['cache' => false, 'tag' => self::TAG, 'expiry' => true]);

		$this->get(self::KEY)->shouldReturn(self::STRING);
	}

	function it_can_forget_a_set_setting()
	{
		$query = Mockery::mock(DatabaseManager::class);
		$query->shouldReceive('table')->andReturn($query);
		$query->shouldReceive('insert')->andReturn(true);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('from')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('whereRaw')->andReturn($query);
		$query->shouldReceive('delete')->andReturn(true);
		$query->shouldReceive('lists')->andReturn(array(self::KEY => json_encode(self::STRING)));
		$query->shouldReceive('count')->andReturn(0);

		$repository = Mockery::mock(Config::class);
		$repository->shouldReceive('get')->andReturn(false);

		$cache = Mockery::mock(Cache::class);
		$this->beConstructedWith($query, $repository, $cache, ['cache' => false, 'tag' => self::TAG, 'expiry' => true]);

		$this->set(self::KEY, self::STRING)->shouldReturn(true);
		$this->get(self::KEY)->shouldReturn(self::STRING);
		$this->forget(self::KEY)->shouldReturn(true);
	}

	function it_can_forget_a_set_setting_with_caching()
	{
		$query = Mockery::mock(DatabaseManager::class);
		$query->shouldReceive('table')->andReturn($query);
		$query->shouldReceive('insert')->andReturn(true);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('from')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('whereRaw')->andReturn($query);
		$query->shouldReceive('delete')->andReturn(true);
		$query->shouldReceive('lists')->andReturn(array(self::KEY => json_encode(self::STRING)));
		$query->shouldReceive('count')->andReturn(0);
		$query->shouldReceive('update')->andReturn(true);

		$repository = Mockery::mock(Config::class);
		$repository->shouldReceive('get')->andReturn(false);

		$cache = Mockery::mock(Cache::class);
		$cache->shouldReceive('add')->andReturn(true);
		$cache->shouldReceive('has')->andReturn(true);
		$cache->shouldReceive('forget')->andReturn(true);
		$cache->shouldReceive('get')->andReturn(self::STRING);

		$this->beConstructedWith($query, $repository, $cache, ['cache' => true, 'tag' => self::TAG, 'expiry' => true]);

		$this->set(self::KEY, self::STRING)->shouldReturn(true);
		$this->get(self::KEY)->shouldReturn(self::STRING);
		$this->forget(self::KEY)->shouldReturn(true);
	}

	function it_cant_forget_an_unset_setting()
	{
		$query = Mockery::mock(DatabaseManager::class);
		$query->shouldReceive('table')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('delete')->andReturn(false);

		$repository = Mockery::mock(Config::class);

		$cache = Mockery::mock(Cache::class);

		$this->beConstructedWith($query, $repository, $cache, ['cache' => false, 'tag' => self::TAG, 'expiry' => true]);

		$this->forget(self::KEY)->shouldReturn(false);
	}

	function it_cant_forget_an_unset_setting_with_caching()
	{
		$query = Mockery::mock(DatabaseManager::class);
		$query->shouldReceive('table')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('delete')->andReturn(false);

		$repository = Mockery::mock(Config::class);

		$cache = Mockery::mock(Cache::class);
		$cache->shouldReceive('has')->andReturn(true);
		$cache->shouldReceive('forget')->andReturn(true);

		$this->beConstructedWith($query, $repository, $cache, ['cache' => true, 'tag' => self::TAG, 'expiry' => true]);

		$this->forget(self::KEY)->shouldReturn(false);
	}

	function it_can_check_a_setting_is_set()
	{
		$query = Mockery::mock(DatabaseManager::class);
		$query->shouldReceive('table')->andReturn($query);
		$query->shouldReceive('update')->andReturn(true);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('count')->andReturn(1);

		$repository = Mockery::mock(Config::class);

		$cache = Mockery::mock(Cache::class);

		$this->beConstructedWith($query, $repository, $cache, ['cache' => false, 'tag' => self::TAG, 'expiry' => true]);

		$this->set(self::KEY, self::STRING)->shouldReturn(true);
		$this->has(self::KEY)->shouldReturn(true);
	}

	function it_can_check_a_setting_is_set_with_caching()
	{
		$query = Mockery::mock(DatabaseManager::class);
		$query->shouldReceive('table')->andReturn($query);
		$query->shouldReceive('update')->andReturn(true);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('count')->andReturn(1);

		$repository = Mockery::mock(Config::class);

		$cache = Mockery::mock(Cache::class);
		$cache->shouldReceive('add')->andReturn(true);
		$cache->shouldReceive('has')->andReturn(true);
		$cache->shouldReceive('forget')->andReturn(true);

		$this->beConstructedWith($query, $repository, $cache, ['cache' => true, 'tag' => self::TAG, 'expiry' => true]);

		$this->set(self::KEY, self::STRING)->shouldReturn(true);
		$this->has(self::KEY)->shouldReturn(true);
	}

	function it_can_check_a_setting_is_not_set()
	{
		$query = Mockery::mock(DatabaseManager::class);
		$query->shouldReceive('table->select->where->count')->andReturn(0);

		$repository = Mockery::mock(Config::class);

		$cache = Mockery::mock(Cache::class);

		$this->beConstructedWith($query, $repository, $cache, ['cache' => false, 'tag' => self::TAG, 'expiry' => true]);

		$this->has(self::KEY)->shouldReturn(false);
	}

	function it_can_check_a_setting_is_not_set_with_caching()
	{
		$query = Mockery::mock(DatabaseManager::class);
		$query->shouldReceive('table->select->where->count')->andReturn(0);

		$repository = Mockery::mock(Config::class);

		$cache = Mockery::mock(Cache::class);
		$cache->shouldReceive('has')->andReturn(false);

		$this->beConstructedWith($query, $repository, $cache, ['cache' => true, 'tag' => self::TAG, 'expiry' => true]);

		$this->has(self::KEY)->shouldReturn(false);
	}

	function it_can_get_all_set_settings()
	{
		$array = array(self::KEY . '1' =>  json_encode(self::STRING), self::KEY . '2' =>  json_encode(self::INTEGER));

		$query = Mockery::mock(DatabaseManager::class);
		$query->shouldReceive('table')->andReturn($query);
		$query->shouldReceive('update')->andReturn(true);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('count')->andReturn(1);
		$query->shouldReceive('lists')->andReturn($array);

		$repository = Mockery::mock(Config::class);

		$cache = Mockery::mock(Cache::class);

		$this->beConstructedWith($query, $repository, $cache, ['cache' => false, 'tag' => self::TAG, 'expiry' => true]);

		$this->set(self::KEY . '1', self::STRING)->shouldReturn(true);
		$this->set(self::KEY . '2', self::STRING)->shouldReturn(true);
		$this->all()->shouldReturn(array(self::KEY . '1' => self::STRING, self::KEY . '2' => self::INTEGER));
	}

	function it_can_get_all_set_settings_with_caching()
	{
		$array = array(self::KEY . '1' =>  json_encode(self::STRING), self::KEY . '2' =>  json_encode(self::INTEGER));

		$query = Mockery::mock(DatabaseManager::class);
		$query->shouldReceive('table')->andReturn($query);
		$query->shouldReceive('update')->andReturn(true);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('count')->andReturn(1);
		$query->shouldReceive('lists')->andReturn($array);

		$repository = Mockery::mock(Config::class);

		$cache = Mockery::mock(Cache::class);
		$cache->shouldReceive('add')->andReturn(true);
		$cache->shouldReceive('has')->andReturn(false);
		$cache->shouldReceive('forget')->andReturn(true);
		$cache->shouldReceive('put')->andReturn(true);
		$cache->shouldReceive('get')->andReturn($array);

		$this->beConstructedWith($query, $repository, $cache, ['cache' => true, 'tag' => self::TAG, 'expiry' => true]);

		$this->set(self::KEY . '1', self::STRING)->shouldReturn(true);
		$this->set(self::KEY . '2', self::STRING)->shouldReturn(true);
		$this->all()->shouldReturn(array(self::KEY . '1' => self::STRING, self::KEY . '2' => self::INTEGER));
	}

	function it_can_clear_all_set_settings()
	{
		$query = Mockery::mock(DatabaseManager::class);
		$query->shouldReceive('table')->andReturn($query);
		$query->shouldReceive('insert')->andReturn(true);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('from')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('count')->andReturn(0);
		$query->shouldReceive('truncate')->andReturn(true);

		$repository = Mockery::mock(Config::class);

		$cache = Mockery::mock(Cache::class);

		$this->beConstructedWith($query, $repository, $cache, ['cache' => false, 'tag' => self::TAG, 'expiry' => true]);

		$this->set(self::KEY, self::STRING)->shouldReturn(true);
		$this->clear()->shouldReturn(true);
		$this->has(self::KEY)->shouldReturn(false);
	}

	function it_can_clear_all_set_settings_with_caching()
	{
		$query = Mockery::mock(DatabaseManager::class);
		$query->shouldReceive('table')->andReturn($query);
		$query->shouldReceive('insert')->andReturn(true);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('from')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('count')->andReturn(0);
		$query->shouldReceive('truncate')->andReturn(true);

		$repository = Mockery::mock(Config::class);

		$cache = Mockery::mock(Cache::class);
		$cache->shouldReceive('add')->andReturn(true);
		$cache->shouldReceive('has')->andReturn(false);
		$cache->shouldReceive('put')->andReturn(true);
		$cache->shouldReceive('flush')->andReturn(true);
		$cache->shouldReceive('forget')->andReturn(true);

		$this->beConstructedWith($query, $repository, $cache, ['cache' => true, 'tag' => self::TAG, 'expiry' => true]);

		$this->set(self::KEY, self::STRING)->shouldReturn(true);
		$this->clear()->shouldReturn(true);
		$this->has(self::KEY)->shouldReturn(false);
	}

	function it_can_get_an_array_child_settings()
	{
		$array = array(self::KEY . '.1' =>  json_encode(self::STRING), self::KEY . '.2' =>  json_encode(self::INTEGER));

		$query = Mockery::mock(DatabaseManager::class);
		$query->shouldReceive('table')->andReturn($query);
		$query->shouldReceive('insert')->andReturn(true);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('whereRaw')->andReturn($query);
		$query->shouldReceive('lists')->andReturn($array);
		$query->shouldReceive('count')->andReturn(0);

		$repository = Mockery::mock(Config::class);
		$repository->shouldReceive('get')->andReturn(false);

		$cache = Mockery::mock(Cache::class);

		$this->beConstructedWith($query, $repository, $cache, ['cache' => false, 'tag' => self::TAG, 'expiry' => true]);

		$this->set(self::KEY . '.1', self::STRING)->shouldReturn(true);
		$this->set(self::KEY . '.2', self::INTEGER)->shouldReturn(true);
		$this->get(self::KEY)->shouldReturn(array('1' => self::STRING, '2' => self::INTEGER));
	}

	function it_can_get_an_array_child_settings_with_caching()
	{
		$array = array(self::KEY . '.1' =>  json_encode(self::STRING), self::KEY . '.2' =>  json_encode(self::INTEGER));

		$query = Mockery::mock(DatabaseManager::class);
		$query->shouldReceive('table')->andReturn($query);
		$query->shouldReceive('insert')->andReturn(true);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('whereRaw')->andReturn($query);
		$query->shouldReceive('lists')->andReturn($array);
		$query->shouldReceive('count')->andReturn(0);

		$repository = Mockery::mock(Config::class);
		$repository->shouldReceive('get')->andReturn(false);

		$cache = Mockery::mock(Cache::class);
		$cache->shouldReceive('add')->andReturn(true);
		$cache->shouldReceive('has')->andReturn(false);
		$cache->shouldReceive('forget')->andReturn(true);

		$this->beConstructedWith($query, $repository, $cache, ['cache' => true, 'tag' => self::TAG, 'expiry' => true]);

		$this->set(self::KEY . '.1', self::STRING)->shouldReturn(true);
		$this->set(self::KEY . '.2', self::INTEGER)->shouldReturn(true);
		$this->get(self::KEY)->shouldReturn(array('1' => self::STRING, '2' => self::INTEGER));
	}

	function it_can_set_an_array_setting()
	{
		$query = Mockery::mock(DatabaseManager::class);
		$query->shouldReceive('table')->andReturn($query);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('get')->andReturn(false);
		$query->shouldReceive('insert')->andReturn(true);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('whereRaw')->andReturn($query);
		$query->shouldReceive('lists')->andReturn($query);
		$query->shouldReceive('count')->andReturn(0);

		$repository = Mockery::mock(Config::class);

		$cache = Mockery::mock(Cache::class);

		$this->beConstructedWith($query, $repository, $cache, ['cache' => false, 'tag' => self::TAG, 'expiry' => true]);

		$this->set(self::KEY, array(self::STRING, self::STRING, self::STRING))->shouldReturn(true);
	}

	function it_can_set_an_array_setting_with_caching()
	{
		$query = Mockery::mock(DatabaseManager::class);
		$query->shouldReceive('table')->andReturn($query);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('get')->andReturn(false);
		$query->shouldReceive('insert')->andReturn(true);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('whereRaw')->andReturn($query);
		$query->shouldReceive('lists')->andReturn($query);
		$query->shouldReceive('count')->andReturn(0);

		$repository = Mockery::mock(Config::class);

		$cache = Mockery::mock(Cache::class);
		$cache->shouldReceive('add')->andReturn(true);
		$cache->shouldReceive('has')->andReturn(false);
		$cache->shouldReceive('forget')->andReturn(true);
		$cache->shouldReceive('get')->andReturn(array(self::STRING, self::STRING, self::STRING));

		$this->beConstructedWith($query, $repository, $cache, ['cache' => true, 'tag' => self::TAG, 'expiry' => true]);

		$this->set(self::KEY, array(self::STRING, self::STRING, self::STRING))->shouldReturn(true);
	}

	function it_can_set_a_setting_in_cache()
	{
		$query = Mockery::mock(DatabaseManager::class);
		$query->shouldReceive('table')->andReturn($query);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('get')->andReturn(false);
		$query->shouldReceive('insert')->andReturn(true);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('count')->andReturn(0);

		$repository = Mockery::mock(Config::class);

		$cache = Mockery::mock(Cache::class);
		$cache->shouldReceive('add')->andReturn(true);
		$cache->shouldReceive('has')->andReturn(false);
		$cache->shouldReceive('forget')->andReturn(true);

		$this->beConstructedWith($query, $repository, $cache, ['cache' => true, 'tag' => self::TAG, 'expiry' => true]);

		$this->set(self::KEY, self::STRING)->shouldReturn(true);
	}

	function it_can_get_a_setting_from_cache()
	{
		$query = Mockery::mock(DatabaseManager::class);
		$query->shouldReceive('table')->andReturn($query);
		$query->shouldReceive('insert')->andReturn(true);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('whereRaw')->andReturn($query);
		$query->shouldReceive('lists')->andReturn($query);
		$query->shouldReceive('get')->andReturn(false);
		$query->shouldReceive('update')->andReturn(self::STRING);

		$repository = Mockery::mock(Config::class);
		$repository->shouldReceive('get')->andReturn(false);

		$cache = Mockery::mock(Cache::class);
		$cache->shouldReceive('add')->andReturn(true);
		$cache->shouldReceive('has')->andReturn(true);
		$cache->shouldReceive('forget')->andReturn(true);
		$cache->shouldReceive('get')->andReturn(self::STRING);

		$this->beConstructedWith($query, $repository, $cache, ['cache' => true, 'tag' => self::TAG, 'expiry' => true]);

		$this->expires(0)->set(self::KEY, self::STRING)->shouldReturn(true);
		$this->get(self::KEY)->shouldReturn(self::STRING);
	}

	function it_can_forget_a_set_setting_in_cache()
	{
		$query = Mockery::mock(DatabaseManager::class);
		$query->shouldReceive('table')->andReturn($query);
		$query->shouldReceive('insert')->andReturn(true);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('from')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('whereRaw')->andReturn($query);
		$query->shouldReceive('lists')->andReturn($query);
		$query->shouldReceive('delete')->andReturn(true);
		$query->shouldReceive('get')->andReturn(false);
		$query->shouldReceive('update')->andReturn(self::STRING);

		$repository = Mockery::mock(Config::class);
		$repository->shouldReceive('get')->andReturn(false);

		$cache = Mockery::mock(Cache::class);
		$cache->shouldReceive('add')->andReturn(true);
		$cache->shouldReceive('has')->andReturn(true);
		$cache->shouldReceive('get')->andReturn(self::STRING);
		$cache->shouldReceive('forget')->andReturn(true);

		$this->beConstructedWith($query, $repository, $cache, ['cache' => true, 'tag' => self::TAG, 'expiry' => true]);

		$this->set(self::KEY, self::STRING)->shouldReturn(true);
		$this->get(self::KEY)->shouldReturn(self::STRING);
		$this->forget(self::KEY)->shouldReturn(true);
	}

	function it_can_set_and_get_a_string_setting()
	{
		$query = Mockery::mock(DatabaseManager::class);
		$query->shouldReceive('table')->andReturn($query);
		$query->shouldReceive('insert')->andReturn(true);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('whereRaw')->andReturn($query);
		$query->shouldReceive('lists')->andReturn(array(self::KEY => json_encode(self::STRING)));
		$query->shouldReceive('count')->andReturn(0);

		$repository = Mockery::mock(Config::class);
		$repository->shouldReceive('get')->andReturn(false);

		$cache = Mockery::mock(Cache::class);

		$this->beConstructedWith($query, $repository, $cache, ['cache' => false, 'tag' => self::TAG, 'expiry' => true]);

		$this->set(self::KEY, self::STRING)->shouldReturn(true);
		$this->get(self::KEY)->shouldReturn(self::STRING);
	}

	function it_can_set_and_get_a_string_setting_with_caching()
	{
		$query = Mockery::mock(DatabaseManager::class);
		$query->shouldReceive('table')->andReturn($query);
		$query->shouldReceive('insert')->andReturn(true);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('whereRaw')->andReturn($query);
		$query->shouldReceive('lists')->andReturn(array(self::KEY => json_encode(self::STRING)));
		$query->shouldReceive('count')->andReturn(0);

		$repository = Mockery::mock(Config::class);
		$repository->shouldReceive('get')->andReturn(false);

		$cache = Mockery::mock(Cache::class);
		$cache->shouldReceive('add')->andReturn(true);
		$cache->shouldReceive('has')->andReturn(false);
		$cache->shouldReceive('forget')->andReturn(true);

		$this->beConstructedWith($query, $repository, $cache, ['cache' => true, 'tag' => self::TAG, 'expiry' => true]);

		$this->set(self::KEY, self::STRING)->shouldReturn(true);
		$this->get(self::KEY)->shouldReturn(self::STRING);
	}

	function it_can_set_and_get_an_integer_setting()
	{
		$query = Mockery::mock(DatabaseManager::class);
		$query->shouldReceive('table')->andReturn($query);
		$query->shouldReceive('insert')->andReturn(true);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('whereRaw')->andReturn($query);
		$query->shouldReceive('lists')->andReturn(array(self::KEY => json_encode(self::INTEGER)));
		$query->shouldReceive('count')->andReturn(0);

		$repository = Mockery::mock(Config::class);
		$repository->shouldReceive('get')->andReturn(false);

		$cache = Mockery::mock(Cache::class);

		$this->beConstructedWith($query, $repository, $cache, ['cache' => false, 'tag' => self::TAG, 'expiry' => true]);

		$this->set(self::KEY, self::INTEGER)->shouldReturn(true);
		$this->get(self::KEY)->shouldReturn(self::INTEGER);
	}

	function it_can_set_and_get_an_integer_setting_with_caching()
	{
		$query = Mockery::mock(DatabaseManager::class);
		$query->shouldReceive('table')->andReturn($query);
		$query->shouldReceive('insert')->andReturn(true);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('whereRaw')->andReturn($query);
		$query->shouldReceive('lists')->andReturn(array(self::KEY => json_encode(self::INTEGER)));
		$query->shouldReceive('count')->andReturn(0);

		$repository = Mockery::mock(Config::class);
		$repository->shouldReceive('get')->andReturn(false);

		$cache = Mockery::mock(Cache::class);
		$cache->shouldReceive('add')->andReturn(true);
		$cache->shouldReceive('has')->andReturn(false);
		$cache->shouldReceive('forget')->andReturn(true);

		$this->beConstructedWith($query, $repository, $cache, ['cache' => true, 'tag' => self::TAG, 'expiry' => true]);

		$this->set(self::KEY, self::INTEGER)->shouldReturn(true);
		$this->get(self::KEY)->shouldReturn(self::INTEGER);
	}

	function it_can_set_and_get_a_boolean_setting()
	{
		$query = Mockery::mock(DatabaseManager::class);
		$query->shouldReceive('table')->andReturn($query);
		$query->shouldReceive('insert')->andReturn(true);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('whereRaw')->andReturn($query);
		$query->shouldReceive('lists')->andReturn(array(self::KEY => json_encode(self::BOOLEAN)));
		$query->shouldReceive('count')->andReturn(0);

		$repository = Mockery::mock(Config::class);
		$repository->shouldReceive('get')->andReturn(false);

		$cache = Mockery::mock(Cache::class);

		$this->beConstructedWith($query, $repository, $cache, ['cache' => false, 'tag' => self::TAG, 'expiry' => true]);

		$this->set(self::KEY, self::BOOLEAN)->shouldReturn(true);
		$this->get(self::KEY)->shouldReturn(self::BOOLEAN);
	}

	function it_can_set_and_get_a_boolean_setting_with_caching()
	{
		$query = Mockery::mock(DatabaseManager::class);
		$query->shouldReceive('table')->andReturn($query);
		$query->shouldReceive('insert')->andReturn(true);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('whereRaw')->andReturn($query);
		$query->shouldReceive('lists')->andReturn(array(self::KEY => json_encode(self::BOOLEAN)));
		$query->shouldReceive('count')->andReturn(0);

		$repository = Mockery::mock(Config::class);
		$repository->shouldReceive('get')->andReturn(false);

		$cache = Mockery::mock(Cache::class);
		$cache->shouldReceive('add')->andReturn(true);
		$cache->shouldReceive('has')->andReturn(false);
		$cache->shouldReceive('forget')->andReturn(true);

		$this->beConstructedWith($query, $repository, $cache, ['cache' => true, 'tag' => self::TAG, 'expiry' => true]);

		$this->set(self::KEY, self::BOOLEAN)->shouldReturn(true);
		$this->get(self::KEY)->shouldReturn(self::BOOLEAN);
	}

	function it_can_set_and_get_an_empty_setting()
	{
		$query = Mockery::mock(DatabaseManager::class);
		$query->shouldReceive('table')->andReturn($query);
		$query->shouldReceive('insert')->andReturn(true);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('whereRaw')->andReturn($query);
		$query->shouldReceive('lists')->andReturn(array(self::KEY => ""));
		$query->shouldReceive('count')->andReturn(0);

		$repository = Mockery::mock(Config::class);
		$repository->shouldReceive('get')->andReturn(false);

		$cache = Mockery::mock(Cache::class);

		$this->beConstructedWith($query, $repository, $cache, ['cache' => false, 'tag' => self::TAG, 'expiry' => true]);

		$this->set(self::KEY, '')->shouldReturn(true);
		$this->get(self::KEY)->shouldReturn('');
	}

	function it_can_set_and_get_an_empty_setting_with_caching()
	{
		$query = Mockery::mock(DatabaseManager::class);
		$query->shouldReceive('table')->andReturn($query);
		$query->shouldReceive('insert')->andReturn(true);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('whereRaw')->andReturn($query);
		$query->shouldReceive('lists')->andReturn(array(self::KEY => ""));
		$query->shouldReceive('count')->andReturn(0);

		$repository = Mockery::mock(Config::class);
		$repository->shouldReceive('get')->andReturn(false);

		$cache = Mockery::mock(Cache::class);
		$cache->shouldReceive('add')->andReturn(true);
		$cache->shouldReceive('has')->andReturn(false);
		$cache->shouldReceive('forget')->andReturn(true);

		$this->beConstructedWith($query, $repository, $cache, ['cache' => true, 'tag' => self::TAG, 'expiry' => true]);

		$this->set(self::KEY, '')->shouldReturn(true);
		$this->get(self::KEY)->shouldReturn('');
	}

	function it_can_set_and_get_an_object_setting()
	{
		$object = new stdClass();
		$object->{self::KEY} = self::STRING;

		$query = Mockery::mock(DatabaseManager::class);
		$query->shouldReceive('table')->andReturn($query);
		$query->shouldReceive('insert')->andReturn(true);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('whereRaw')->andReturn($query);
		$query->shouldReceive('lists')->andReturn(array(self::KEY => json_encode($object)));
		$query->shouldReceive('count')->andReturn(0);

		$repository = Mockery::mock(Config::class);
		$repository->shouldReceive('get')->andReturn(false);

		$cache = Mockery::mock(Cache::class);

		$this->beConstructedWith($query, $repository, $cache, ['cache' => false, 'tag' => self::TAG, 'expiry' => true]);

		$this->set(self::KEY, $object)->shouldReturn(true);
		$this->get(self::KEY)->shouldHaveType($object);
	}

	function it_can_set_and_get_an_object_setting_with_caching()
	{
		$object = new stdClass();
		$object->{self::KEY} = self::STRING;

		$query = Mockery::mock(DatabaseManager::class);
		$query->shouldReceive('table')->andReturn($query);
		$query->shouldReceive('insert')->andReturn(true);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('whereRaw')->andReturn($query);
		$query->shouldReceive('lists')->andReturn(array(self::KEY => json_encode($object)));
		$query->shouldReceive('count')->andReturn(0);

		$repository = Mockery::mock(Config::class);
		$repository->shouldReceive('get')->andReturn(false);

		$cache = Mockery::mock(Cache::class);
		$cache->shouldReceive('add')->andReturn(true);
		$cache->shouldReceive('has')->andReturn(false);
		$cache->shouldReceive('forget')->andReturn(true);

		$this->beConstructedWith($query, $repository, $cache, ['cache' => true, 'tag' => self::TAG, 'expiry' => true]);

		$this->set(self::KEY, $object)->shouldReturn(true);
		$this->get(self::KEY)->shouldHaveType($object);
	}

	function it_can_merge_database_settings_with_config_settings()
	{
		$config = array(self::KEY . 1 => self::BOOLEAN, self::KEY . 2 => self::INTEGER);
		$expectedConfig = array(self::KEY . 1 => self::STRING, self::KEY . 2 => self::INTEGER);

		$query = Mockery::mock(DatabaseManager::class);
		$query->shouldReceive('table')->andReturn($query);
		$query->shouldReceive('insert')->andReturn(true);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('whereRaw')->andReturn($query);
		$query->shouldReceive('lists')->twice()->andReturnValues(
			array(false, array(self::KEY . '.' . self::KEY . 1 => self::STRING))
		);
		$query->shouldReceive('count')->andReturn(0);

		$repository = Mockery::mock(Config::class);
		$repository->shouldReceive('has')->andReturn(true);
		$repository->shouldReceive('get')->andReturn($config);

		$cache = Mockery::mock(Cache::class);

		$this->beConstructedWith($query, $repository, $cache, ['cache' => false, 'tag' => self::TAG, 'expiry' => true]);

		$this->get(self::KEY)->shouldReturn($config);
		$this->set(self::KEY . '.' . self::KEY . 1, self::STRING)->shouldReturn(true);
		$this->get(self::KEY)->shouldReturn($expectedConfig);
	}

	function it_can_recursively_merge_database_settings_with_config_settings()
	{
		$config = array(
			self::KEY . 1 => self::BOOLEAN,
			self::KEY . 2 => array(self::KEY . 1 => self::INTEGER, self::KEY . 2 => self::STRING)
		);
		$expectedConfig = array(
			self::KEY . 1 => self::BOOLEAN,
			self::KEY . 2 => array(self::KEY . 1 => self::STRING, self::KEY . 2 => self::STRING)
		);

		$query = Mockery::mock(DatabaseManager::class);
		$query->shouldReceive('table')->andReturn($query);
		$query->shouldReceive('insert')->andReturn(true);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('whereRaw')->andReturn($query);
		$query->shouldReceive('lists')->twice()->andReturnValues(
			array(false, array(self::KEY . '.' . self::KEY . 2 . '.' . self::KEY . 1 => self::STRING))
		);
		$query->shouldReceive('count')->andReturn(0);

		$repository = Mockery::mock(Config::class);
		$repository->shouldReceive('has')->andReturn(true);
		$repository->shouldReceive('get')->andReturn($config);

		$cache = Mockery::mock(Cache::class);

		$this->beConstructedWith($query, $repository, $cache, ['cache' => false, 'tag' => self::TAG, 'expiry' => true]);

		$this->get(self::KEY)->shouldReturn($config);
		$this->set(self::KEY . '.' . self::KEY . 2 . '.' . self::KEY . 1, self::STRING)->shouldReturn(true);
		$this->get(self::KEY)->shouldReturn($expectedConfig);
	}

	function it_can_forget_a_cached_setting()
	{
		$query = Mockery::mock(DatabaseManager::class);
		$query->shouldReceive('table')->andReturn($query);
		$query->shouldReceive('insert')->andReturn(true);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('whereRaw')->andReturn($query);
		$query->shouldReceive('lists')->andReturn(array(self::KEY => json_encode(self::BOOLEAN)));
		$query->shouldReceive('count')->andReturn(0);

		$repository = Mockery::mock(Config::class);
		$repository->shouldReceive('get')->andReturn(false);

		$cache = Mockery::mock(Cache::class);
		$cache->shouldReceive('add')->andReturn(true);
		$cache->shouldReceive('has')->andReturn(false);
		$cache->shouldReceive('forget')->andReturn(true);

		$this->beConstructedWith($query, $repository, $cache, ['cache' => true, 'tag' => self::TAG, 'expiry' => true]);

		$this->set(self::KEY, self::BOOLEAN)->shouldReturn(true);
		$this->get(self::KEY)->shouldReturn(self::BOOLEAN);
		$this->cacheForget(self::KEY)->shouldReturn(self::BOOLEAN);
	}

	function it_can_forget_all_cached_setting()
	{
		$query = Mockery::mock(DatabaseManager::class);
		$query->shouldReceive('table')->andReturn($query);
		$query->shouldReceive('insert')->andReturn(true);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('whereRaw')->andReturn($query);
		$query->shouldReceive('lists')->andReturn(array(self::KEY => json_encode(self::BOOLEAN)));
		$query->shouldReceive('count')->andReturn(0);

		$repository = Mockery::mock(Config::class);
		$repository->shouldReceive('get')->andReturn(false);

		$cache = Mockery::mock(Cache::class);
		$cache->shouldReceive('add')->andReturn(true);
		$cache->shouldReceive('has')->andReturn(false);
		$cache->shouldReceive('forget')->andReturn(true);
		$cache->shouldReceive('flush')->andReturn(true);

		$this->beConstructedWith($query, $repository, $cache, ['cache' => true, 'tag' => self::TAG, 'expiry' => true]);

		$this->set(self::KEY, self::BOOLEAN)->shouldReturn(true);
		$this->get(self::KEY)->shouldReturn(self::BOOLEAN);
		$this->cacheClear()->shouldReturn(self::BOOLEAN);
	}

	function it_can_merge_a_set_setting_with_config()
	{
		$query = Mockery::mock(DatabaseManager::class);
		$query->shouldReceive('table')->andReturn($query);
		$query->shouldReceive('insert')->andReturn(true);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('whereRaw')->andReturn($query);
		$query->shouldReceive('lists')->andReturn(array(self::SUB_KEY_ITEM_1 => json_encode(self::BOOLEAN)));
		$query->shouldReceive('count')->andReturn(0);

		$repository = Mockery::mock(Config::class);
		$repository->shouldReceive('has')->andReturn(true);
		$repository->shouldReceive('get')->andReturn(array(self::VALUE_1 => self::BOOLEAN, self::VALUE_2 => self::STRING));

		$cache = Mockery::mock(Cache::class);

		$this->beConstructedWith($query, $repository, $cache, ['cache' => false, 'tag' => self::TAG, 'expiry' => true]);

		$this->set(self::SUB_KEY_ITEM_1, self::BOOLEAN)->shouldReturn(true);
		$this->get(self::SUB_KEY)->shouldReturn(array(self::VALUE_1 => true, self::VALUE_2 => self::STRING));
	}

	function it_can_return_an_array_when_when_returning_a_sub_setting()
	{
		$query = Mockery::mock(DatabaseManager::class);
		$query->shouldReceive('table')->andReturn($query);
		$query->shouldReceive('insert')->andReturn(true);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('whereRaw')->andReturn($query);
		$query->shouldReceive('lists')->andReturn(array(self::SUB_KEY_ITEM_1 => json_encode(self::BOOLEAN)));
		$query->shouldReceive('count')->andReturn(0);

		$repository = Mockery::mock(Config::class);
		$repository->shouldReceive('has')->andReturn(false);
		$repository->shouldReceive('get')->andReturn(false);

		$cache = Mockery::mock(Cache::class);

		$this->beConstructedWith($query, $repository, $cache, ['cache' => false, 'tag' => self::TAG, 'expiry' => true]);

		$this->set(self::SUB_KEY_ITEM_1, self::BOOLEAN)->shouldReturn(true);
		$this->get(self::SUB_KEY)->shouldReturn(array(self::VALUE_1 => true));
	}

	function it_can_return_an_array_when_when_returning_a_sub_setting_with_caching()
	{
		$query = Mockery::mock(DatabaseManager::class);
		$query->shouldReceive('table')->andReturn($query);
		$query->shouldReceive('insert')->andReturn(true);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('whereRaw')->andReturn($query);
		$query->shouldReceive('lists')->andReturn(array(self::SUB_KEY_ITEM_1 => json_encode(self::BOOLEAN)));
		$query->shouldReceive('count')->andReturn(0);
		$query->shouldReceive('update')->andReturn(true);

		$repository = Mockery::mock(Config::class);
		$repository->shouldReceive('has')->andReturn(false);
		$repository->shouldReceive('get')->andReturn(false);

		$cache = Mockery::mock(Cache::class);
		$cache->shouldReceive('has')->andReturn(true);
		$cache->shouldReceive('forget')->andReturn(true);
		$cache->shouldReceive('add')->andReturn(true);
		$cache->shouldReceive('get')->andReturn(array(self::VALUE_1 => self::BOOLEAN));

		$this->beConstructedWith($query, $repository, $cache, ['cache' => true, 'tag' => self::TAG, 'expiry' => true]);

		$this->set(self::SUB_KEY_ITEM_1, self::BOOLEAN)->shouldReturn(true);
		$this->get(self::SUB_KEY)->shouldReturn(array(self::VALUE_1 => true));
	}

	function it_set_and_get_an_uncached_item()
	{
		$query = Mockery::mock(DatabaseManager::class);
		$query->shouldReceive('table')->andReturn($query);
		$query->shouldReceive('insert')->andReturn(true);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('whereRaw')->andReturn($query);
		$query->shouldReceive('lists')->andReturn(array(self::SUB_KEY_ITEM_1 => json_encode(self::BOOLEAN)));
		$query->shouldReceive('count')->andReturn(0);
		$query->shouldReceive('update')->andReturn(true);

		$repository = Mockery::mock(Config::class);
		$repository->shouldReceive('has')->andReturn(false);
		$repository->shouldReceive('get')->andReturn(false);

		$cache = Mockery::mock(Cache::class);
		$cache->shouldReceive('has')->andReturn(true);
		$cache->shouldReceive('forget')->andReturn(true);
		$cache->shouldReceive('add')->andReturn(true);

		$this->beConstructedWith($query, $repository, $cache, ['cache' => true, 'tag' => self::TAG, 'expiry' => true]);

		$this->set(self::SUB_KEY_ITEM_1, self::BOOLEAN)->shouldReturn(true);
		$this->uncached()->get(self::SUB_KEY)->shouldReturn(array(self::VALUE_1 => true));
	}
}
