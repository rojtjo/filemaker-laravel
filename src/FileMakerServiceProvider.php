<?php

namespace FileMaker\Laravel;

use Illuminate\Support\ServiceProvider;
use FileMaker\FileMaker as FM;
use FileMaker\Parser\Parser;
use FileMaker\Server;
use Illuminate\Foundation\Application;

class FileMakerServiceProvider extends ServiceProvider
{
	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = true;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->registerResources();
		$this->registerServers();
		$this->setDefaultServer();
	}

	/**
	 * Registers the package resources and handles publishing of package resources.
	 *
	 * @return void
	 */
	private function registerResources()
	{
		if ($this->isLaravel5()) {
			$this->mergeConfigFrom(__DIR__.'/config/config.php', 'filemaker-laravel.config');

			$this->publishes([
				__DIR__.'/config' => config_path('filemaker-laravel'),
			], 'config');
		} else {
			$this->package('rojtjo/filemaker-laravel', 'filemaker-laravel', __DIR__);
		}
	}

	/**
	 * @return void
	 */
	private function registerServers()
	{
		$key = $this->isLaravel5() ?
			'filemaker-laravel.config.connections' :
			'filemaker-laravel::connections';

		$connections = $this->app['config']->get($key, array());
		$fm = $this->app['filemaker'];

		foreach($connections as $name => $connection) {
			$fm->addServer($name, new Server(
				array_get($connection, 'host'),
				array_get($connection, 'database'),
				array_get($connection, 'port', 80),
				array_get($connection, 'username'),
				array_get($connection, 'password')
			));
		}
	}

	/**
	 * @return void
	 */
	private function setDefaultServer()
	{
		$key = $this->isLaravel5() ?
			'filemaker-laravel.config.default' :
			'filemaker-laravel::default';

		$name = $this->app['config']->get($key);
		$this->app['filemaker']->setDefaultServer($name);
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		if ($this->isLaravel5()) {
			$this->app->singleton('filemaker', function ($app) {
				$parser = $app[Parser::class];

				return new FM($parser);
			});
		} else {
			$this->app['filemaker'] = $this->app->share(function($app) {
				$parser = $app[Parser::class];

				return new FM($parser);
			});
		}

		$this->app->alias('filemaker', 'FileMaker\FileMaker');
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array(
			'filemaker',
			'FileMaker\FileMaker'
		);
	}

	/**
	 * @return bool
	 */
	private function isLaravel5()
	{
		return version_compare(Application::VERSION, '5.0.0') >= 0;
	}
}
