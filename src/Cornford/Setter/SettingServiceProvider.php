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
		$this->publishes(
			[
				__DIR__ . '/../../config/config.php' => config_path('setter.php'),
				__DIR__ . '/../../migrations/' => database_path('/migrations')
			],
			'setter'
		);
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$configPath = __DIR__ . '/../../config/config.php';
		$this->mergeConfigFrom($configPath, 'googlmapper');

		$this->app->singleton(
		    'setting',
            function($app)
            {
                return new Setting(
                    $this->app->make('Illuminate\Database\DatabaseManager'),
                    $this->app->make('Illuminate\Config\Repository'),
                    $this->app->make('Illuminate\Cache\Repository'),
                    $app['config']->get('googlmapper')
                );
            }
		);
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
