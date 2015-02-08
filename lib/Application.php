<?php

namespace SlimMVC;

/**
 * Application class
 */
class Application {
	/**
	 * @var $loader
	 */
	protected $loader = null;

	/**
	 * @var $modulePath
	 */
	protected $modulePath = null;

	/**
	 * @var $modules
	 */
	protected $modules = null;

	/**
	 * @var $app
	 */
	protected $app = null;

	/**
	 * constructor
	 *
	 * @param string $loader Composer Autoloader
	 * @param string $modulePath path with modules
	 * @param array $modules array with modules to load
	 */
	public function __construct($loader, $modulePath, $modules) {
		// loader
		$this->loader = $loader;

		// module path
		$this->modulePath = $modulePath;

		// modules
		$this->modules = $modules;

		// bootstrap
		$this->bootstrap();
	}

	/**
	 * run application
	 */
	public function run() {
		$this->app->run();
	}

	/**
	 * boostrap the app
	 */
	protected function bootstrap() {
		$this->initSlim();
		$this->initModules();
	}

	/**
	 * init slim
	 */
	protected function initSlim() {
		// init slim app
		$this->app = new \Slim\Slim();
	}

	/**
	 * bootstrap modules
	 */
	protected function initModules() {
		// app
		$app = $this->app;

                // navigation
                $navigation = array();

		// view paths
		$viewModules = array();

		// for each module
		foreach($this->modules as $module) {
			require_once($this->modulePath . $module . "/Module.php");
			$m = "\\$module\\Module";
			$m = new $m;

			// autoloader
			if(method_exists($m, 'getAutoloaderConfig')) {
				$cfg = $m->getAutoloaderConfig();

				// register module namespace
				// @see: https://getcomposer.org/doc/01-basic-usage.md#autoloading
				foreach($cfg as $key => $value) {
					$loader->add($key,  $value);
				}
			}

			// routes
			if(method_exists($m, 'getRouterConfig')) {
				$cfg = $m->getRouterConfig();

				// load routes
				foreach($cfg as $value) {
					foreach (glob($value . "/*.php") as $filename) {
						require_once($filename);
					}
				}
			}

			// views
			if(method_exists($m, 'getViewConfig')) {
				$cfg = $m->getViewConfig();

				// get view paths
				foreach($cfg as $key => $value) {
					// get module view path
					// store path in app to register it with template engine
					$viewModules[$key] = $value;
				}
			}

			// navigation
			if(method_exists($m, 'getNavigationConfig')) {
                                // get navigation config
				$cfg = $m->getNavigationConfig();

                                // merge navigation recursiv
				$navigation = array_merge_recursive($navigation, $cfg);
			}
		}

                // register namespaces
		$loader->register();

		// save view modules
		$app->container->singleton('viewModules', $viewModules);

                // save navigation
                $app->container->singletin('navigation', $navigation);
	}
}
