<?php namespace FileMaker\Laravel;

use Illuminate\Support\ServiceProvider;
use FileMaker\FileMaker\Server;

class FileMakerServiceProvider extends ServiceProvider {

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
		foreach($this->app['config']->get('database.connections') as $name => $server) {

			if(array_get($server, 'driver') === 'filemaker') {
				$this->app['filemaker']->addServer($name, new Server(
					array_get($server, 'host'),
					array_get($server, 'database'),
					array_get($server, 'port', 80),
					array_get($server, 'username'),
					array_get($server, 'password')
				));
			}
		}
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bindShared('filemaker', 'FileMaker\FileMaker');
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array(
			'FileMaker\FileMaker'
		);
	}
}