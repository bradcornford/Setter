<?php namespace spec\Cornford\Setter;

use PhpSpec\ObjectBehavior;
use Mockery;

class SettingSpec extends ObjectBehavior
{
	function it_is_initializable()
	{
		$query = Mockery::mock('Illuminate\Database\DatabaseManager');
		$repository = Mockery::mock('Illuminate\Config\Repository');
		$cache = Mockery::mock('Illuminate\Cache\Repository');
		$this->beConstructedWith($query, $repository, $cache);

		$this->shouldHaveType('Cornford\Setter\Setting');
	}

	function it_can_set_a_setting()
	{
		$query = Mockery::mock('Illuminate\Database\DatabaseManager');
		$query->shouldReceive('table')->andReturn($query);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('get')->andReturn(false);
		$query->shouldReceive('insert')->andReturn(true);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('get')->andReturn(false);

		$repository = Mockery::mock('Illuminate\Config\Repository');

		$cache = Mockery::mock('Illuminate\Cache\Repository');
		$cache->shouldReceive('add')->andReturn(true);

		$this->beConstructedWith($query, $repository, $cache);

		$this->set('test', 'thevalue')->shouldReturn(true);
	}

	function it_can_get_a_setting()
	{
		$query = Mockery::mock('Illuminate\Database\DatabaseManager');
		$query->shouldReceive('table')->andReturn($query);
		$query->shouldReceive('insert')->andReturn(true);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('whereRaw')->andReturn($query);
		$query->shouldReceive('lists')->andReturn(array('test' => '"thevalue"'));
		$query->shouldReceive('get')->andReturn(false);

		$repository = Mockery::mock('Illuminate\Config\Repository');

		$cache = Mockery::mock('Illuminate\Cache\Repository');
		$cache->shouldReceive('add')->andReturn(true);
		$cache->shouldReceive('has')->andReturn(false);

		$this->beConstructedWith($query, $repository, $cache);

		$this->set('test', 'thevalue')->shouldReturn(true);
		$this->get('test')->shouldReturn('thevalue');
	}

	function it_can_set_an_already_set_setting()
	{
		$query = Mockery::mock('Illuminate\Database\DatabaseManager');
		$query->shouldReceive('table')->andReturn($query);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('get')->andReturn(true);
		$query->shouldReceive('update')->andReturn(true);

		$repository = Mockery::mock('Illuminate\Config\Repository');

		$cache = Mockery::mock('Illuminate\Cache\Repository');
		$cache->shouldReceive('add')->andReturn(true);
		$cache->shouldReceive('has')->andReturn(false);

		$this->beConstructedWith($query, $repository, $cache);

		$this->set('test', 'thevalue')->shouldReturn(true);
	}

	function it_can_get_a_setting_with_a_default_value()
	{
		$query = Mockery::mock('Illuminate\Database\DatabaseManager');
		$query->shouldReceive('table')->andReturn($query);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('whereRaw')->andReturn($query);
		$query->shouldReceive('get')->andReturn(false);
		$query->shouldReceive('lists')->andReturn(false);

		$repository = Mockery::mock('Illuminate\Config\Repository');

		$cache = Mockery::mock('Illuminate\Cache\Repository');
		$cache->shouldReceive('add')->andReturn(true);
		$cache->shouldReceive('has')->andReturn(false);

		$this->beConstructedWith($query, $repository, $cache);

		$this->get('test', 'thevalue')->shouldReturn('thevalue');
	}

	function it_can_get_a_setting_from_a_config_value()
	{
		$query = Mockery::mock('Illuminate\Database\DatabaseManager');
		$query->shouldReceive('table')->andReturn($query);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('whereRaw')->andReturn($query);
		$query->shouldReceive('lists')->andReturn(false);

		$repository = Mockery::mock('Illuminate\Config\Repository');
		$repository->shouldReceive('has')->andReturn(true);
		$repository->shouldReceive('get')->andReturn('thevalue');

		$cache = Mockery::mock('Illuminate\Cache\Repository');
		$cache->shouldReceive('add')->andReturn(true);
		$cache->shouldReceive('has')->andReturn(false);

		$this->beConstructedWith($query, $repository, $cache);

		$this->get('testconfig')->shouldReturn('thevalue');
	}

	function it_can_forget_a_set_setting()
	{
		$query = Mockery::mock('Illuminate\Database\DatabaseManager');
		$query->shouldReceive('table')->andReturn($query);
		$query->shouldReceive('insert')->andReturn(true);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('from')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('whereRaw')->andReturn($query);
		$query->shouldReceive('delete')->andReturn(true);
		$query->shouldReceive('lists')->andReturn(array('test' => '"thevalue"'));
		$query->shouldReceive('get')->andReturn(false);

		$repository = Mockery::mock('Illuminate\Config\Repository');

		$cache = Mockery::mock('Illuminate\Cache\Repository');
		$cache->shouldReceive('add')->andReturn(true);
		$cache->shouldReceive('has')->andReturn(false);
		$cache->shouldReceive('forget')->andReturn(true);

		$this->beConstructedWith($query, $repository, $cache);

		$this->set('test', 'thevalue')->shouldReturn(true);
		$this->get('test')->shouldReturn('thevalue');
		$this->forget('test')->shouldReturn(true);
	}

	function it_cant_forget_an_unset_setting()
	{
		$query = Mockery::mock('Illuminate\Database\DatabaseManager');
		$query->shouldReceive('table')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('delete')->andReturn(false);

		$repository = Mockery::mock('Illuminate\Config\Repository');

		$cache = Mockery::mock('Illuminate\Cache\Repository');
		$cache->shouldReceive('forget')->andReturn(true);

		$this->beConstructedWith($query, $repository, $cache);

		$this->forget('test')->shouldReturn(false);
	}

	function it_can_check_a_setting_is_set()
	{
		$query = Mockery::mock('Illuminate\Database\DatabaseManager');
		$query->shouldReceive('table')->andReturn($query);
		$query->shouldReceive('update')->andReturn(true);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('get')->andReturn(true);

		$repository = Mockery::mock('Illuminate\Config\Repository');

		$cache = Mockery::mock('Illuminate\Cache\Repository');
		$cache->shouldReceive('add')->andReturn(true);
		$cache->shouldReceive('has')->andReturn(false);

		$this->beConstructedWith($query, $repository, $cache);

		$this->set('test', 'thevalue')->shouldReturn(true);
		$this->has('test')->shouldReturn(true);
	}

	function it_can_check_a_setting_is_not_set()
	{
		$query = Mockery::mock('Illuminate\Database\DatabaseManager');
		$query->shouldReceive('table->select->where->get')->andReturn(false);

		$repository = Mockery::mock('Illuminate\Config\Repository');

		$cache = Mockery::mock('Illuminate\Cache\Repository');

		$this->beConstructedWith($query, $repository, $cache);

		$this->has('test')->shouldReturn(false);
	}

	function it_can_get_all_set_settings()
	{
		$query = Mockery::mock('Illuminate\Database\DatabaseManager');
		$query->shouldReceive('table')->andReturn($query);
		$query->shouldReceive('update')->andReturn(true);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('get')->andReturn($query);
		$query->shouldReceive('lists')->andReturn(array('test1' => '"thevalue"', 'test2' => '"thevalue"'));

		$repository = Mockery::mock('Illuminate\Config\Repository');

		$cache = Mockery::mock('Illuminate\Cache\Repository');
		$cache->shouldReceive('add')->andReturn(true);
		$cache->shouldReceive('has')->andReturn(false);
		$cache->shouldReceive('put')->andReturn(true);
		$cache->shouldReceive('get')->andReturn(array('test1' => '"thevalue"', 'test2' => '"thevalue"'));

		$this->beConstructedWith($query, $repository, $cache);

		$this->set('test1', 'thevalue')->shouldReturn(true);
		$this->set('test2', 'thevalue')->shouldReturn(true);
		$this->all()->shouldReturn(array('test1' => 'thevalue', 'test2' => 'thevalue'));
	}

	function it_can_clear_all_set_settings()
	{
		$query = Mockery::mock('Illuminate\Database\DatabaseManager');
		$query->shouldReceive('table')->andReturn($query);
		$query->shouldReceive('insert')->andReturn(true);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('from')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('get')->andReturn(false);
		$query->shouldReceive('truncate')->andReturn(true);

		$repository = Mockery::mock('Illuminate\Config\Repository');

		$cache = Mockery::mock('Illuminate\Cache\Repository');
		$cache->shouldReceive('add')->andReturn(true);
		$cache->shouldReceive('has')->andReturn(false);
		$cache->shouldReceive('put')->andReturn(true);
		$cache->shouldReceive('flush')->andReturn(true);

		$this->beConstructedWith($query, $repository, $cache);

		$this->set('test', 'thevalue')->shouldReturn(true);
		$this->clear()->shouldReturn(true);
		$this->has('test')->shouldReturn(false);
	}

	function it_can_get_an_array_child_settings()
	{
		$query = Mockery::mock('Illuminate\Database\DatabaseManager');
		$query->shouldReceive('table')->andReturn($query);
		$query->shouldReceive('insert')->andReturn(true);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('whereRaw')->andReturn($query);
		$query->shouldReceive('lists')->andReturn(array('test.test1' => '"thevalue"', 'test.test2' => '"thevalue"'));
		$query->shouldReceive('get')->andReturn(false);

		$repository = Mockery::mock('Illuminate\Config\Repository');

		$cache = Mockery::mock('Illuminate\Cache\Repository');
		$cache->shouldReceive('add')->andReturn(true);
		$cache->shouldReceive('has')->andReturn(false);

		$this->beConstructedWith($query, $repository, $cache);

		$this->set('test.test1', 'thevalue')->shouldReturn(true);
		$this->set('test.test2', 'thevalue')->shouldReturn(true);
		$this->get('test')->shouldReturn(array('test1' => 'thevalue', 'test2' => 'thevalue'));
	}

	function it_can_set_an_array_setting()
	{
		$query = Mockery::mock('Illuminate\Database\DatabaseManager');
		$query->shouldReceive('table')->andReturn($query);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('get')->andReturn(false);
		$query->shouldReceive('insert')->andReturn(true);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('get')->andReturn(false);

		$repository = Mockery::mock('Illuminate\Config\Repository');

		$cache = Mockery::mock('Illuminate\Cache\Repository');
		$cache->shouldReceive('add')->andReturn(true);
		$cache->shouldReceive('has')->andReturn(false);

		$this->beConstructedWith($query, $repository, $cache);

		$this->set('test', array('thevalue', 'thevalue', 'thevalue'))->shouldReturn(true);
	}

	function it_can_set_a_setting_in_cache()
	{
		$query = Mockery::mock('Illuminate\Database\DatabaseManager');
		$query->shouldReceive('table')->andReturn($query);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('get')->andReturn(false);
		$query->shouldReceive('insert')->andReturn(true);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('get')->andReturn(false);

		$repository = Mockery::mock('Illuminate\Config\Repository');

		$cache = Mockery::mock('Illuminate\Cache\Repository');
		$cache->shouldReceive('add')->andReturn(true);

		$this->beConstructedWith($query, $repository, $cache);

		$this->set('test', 'thevalue')->shouldReturn(true);
	}

	function it_can_get_a_setting_from_cache()
	{
		$query = Mockery::mock('Illuminate\Database\DatabaseManager');
		$query->shouldReceive('table')->andReturn($query);
		$query->shouldReceive('insert')->andReturn(true);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('whereRaw')->andReturn($query);
		$query->shouldReceive('get')->andReturn(false);

		$repository = Mockery::mock('Illuminate\Config\Repository');

		$cache = Mockery::mock('Illuminate\Cache\Repository');
		$cache->shouldReceive('add')->andReturn(true);
		$cache->shouldReceive('has')->andReturn(true);
		$cache->shouldReceive('get')->andReturn('thevalue');

		$this->beConstructedWith($query, $repository, $cache);

		$this->set('test', 'thevalue')->shouldReturn(true);
		$this->get('test')->shouldReturn('thevalue');
	}

	function it_can_forget_a_set_setting_in_cache()
	{
		$query = Mockery::mock('Illuminate\Database\DatabaseManager');
		$query->shouldReceive('table')->andReturn($query);
		$query->shouldReceive('insert')->andReturn(true);
		$query->shouldReceive('select')->andReturn($query);
		$query->shouldReceive('from')->andReturn($query);
		$query->shouldReceive('where')->andReturn($query);
		$query->shouldReceive('whereRaw')->andReturn($query);
		$query->shouldReceive('delete')->andReturn(true);
		$query->shouldReceive('get')->andReturn(false);

		$repository = Mockery::mock('Illuminate\Config\Repository');

		$cache = Mockery::mock('Illuminate\Cache\Repository');
		$cache->shouldReceive('add')->andReturn(true);
		$cache->shouldReceive('has')->andReturn(true);
		$cache->shouldReceive('get')->andReturn('thevalue');
		$cache->shouldReceive('forget')->andReturn(true);

		$this->beConstructedWith($query, $repository, $cache);

		$this->set('test', 'thevalue')->shouldReturn(true);
		$this->get('test')->shouldReturn('thevalue');
		$this->forget('test')->shouldReturn(true);
	}
}
