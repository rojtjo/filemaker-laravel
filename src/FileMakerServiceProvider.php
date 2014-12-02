<?php namespace FileMaker\Laravel;

use Illuminate\Support\ServiceProvider;
use FileMaker\FileMaker as FM;
use FileMaker\Server;

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
		$this->package('rojtjo/filemaker', 'filemaker', __DIR__.'/..');
		$this->registerServers();
		$this->setDefaultServer();
	}

	/**
	 * @return void
	 */
	private function registerServers()
	{
		$connections = $this->app['config']->get('filemaker::connections', array());
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
		$name = $this->app['config']->get('filemaker::default');
		$this->app['filemaker']->setDefaultServer($name);
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['filemaker'] = $this->app->share(function($app) {
			$parser = $app['FileMaker\Parser\Parser'];

			return new FM($parser);
		});

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
}
