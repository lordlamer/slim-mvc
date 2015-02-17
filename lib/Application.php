<?php

namespace SlimMVC;

/**
 * Application class
 */
class Application {
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
	 * @param string $modulePath path with modules
	 * @param array $modules array with modules to load
	 */
	public function __construct($modulePath, $modules) {
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

		// run hook slim.mvc.start
		$app->applyHook('slim.mvc.start');

                // navigation
                $navigation = array();

		// view paths
		$viewModules = array();

		// loader
		$loader = new \Zend\Loader\StandardAutoloader();

		// for each module
		foreach($this->modules as $module) {
			require_once($this->modulePath . '/' . $module . "/Module.php");
			$m = "\\$module\\Module";
			$m = new $m;

			// autoloader
			if(method_exists($m, 'getAutoloaderConfig')) {
				$cfg = $m->getAutoloaderConfig();

				// register module namespace
				foreach($cfg as $key => $value) {
					$loader->registerNamespace($key, $value);
				}

				// register namespaces
				$loader->register();
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

			// hooks
			if(method_exists($m, 'getHookConfig')) {
				$cfg = $m->getHookConfig();

				// load routes
				foreach($cfg as $value) {
					foreach (glob($value . "/*.php") as $filename) {
						require_once($filename);
					}
				}
			}
		}

		// save view modules
		$app->viewModules = $viewModules;

                // save navigation
                $app->navigation = $navigation;

		// run hook slim.mvc.ready
		$app->applyHook('slim.mvc.ready');
	}
}
