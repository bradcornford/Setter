<?php namespace Cornford\Setter;

use Illuminate\Support\ServiceProvider;

class SettingServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('cornford/setter');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['setting'] = $this->app->share(function()
		{
			return new Setting(
				$this->app->make('Illuminate\Database\DatabaseManager'),
				$this->app->make('Illuminate\Config\Repository'),
				$this->app->make('Illuminate\Cache\Repository')
			);
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('setting');
	}

}
