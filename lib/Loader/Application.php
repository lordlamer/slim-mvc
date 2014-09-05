<?php

namespace IIM\Loader;

use Zend\Config\Reader\Ini as ConfigReader;
use Zend\Config\Config;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/**
 * Application class
 */
class Application {
	/**
	 * @var $configFile
	 */
	protected $configFile = null;

	/**
	 * @var $app
	 */
	protected $app = null;

	/**
	 * constructor
	 *
	 * @param string $appConfig application configuration
	 */
	public function __construct($appConfig) {
		// save config
		$this->configFile = $appConfig;

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
		$this->initConfig();
		$this->initDatabase();
		$this->initLog();
		$this->initLayout();
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
	 * bootstrap config
	 */
	protected function initConfig() {
		// init config
		$configReader = new ConfigReader();
		$configData = $configReader->fromFile($this->configFile);
		$config = new Config($configData, true);

		// save config
		$this->app->config = $config;
	}

	/**
	 * bootstrap database
	 */
	protected function initDatabase() {
		// get config
		$config = $this->app->config;

		// create database connection
		$this->app->db = new \PDO($config->database->dsn, $config->database->user, $config->database->password);
	}

	/**
	 * bootstrap log
	 */
	protected function initLog() {
		// get config
		$config = $this->app->config;

		// init logger
		$logger = new Logger('PIM');
		$logger->pushHandler(new StreamHandler($config->log->file, $config->log->level));

		// save log
		$this->app->log = $logger;
	}

	/**
	 * init slim layout
	 */
	protected function initLayout() {
		// get slim app
		$app = $this->app;

		//\Twig_Autoloader::register();
		// twig filesystem loader
		$loader = new \Twig_Loader_Filesystem();

		// load twig with filesystem loader
		$twig = new \Twig_Environment($loader, array(
			'debug' => true,
			'cache' => PROJECT_PATH . '/data/cache'
		));

		// save twig
		$app->twig = $twig;
	}

	/**
	 * bootstrap modules
	 */
	protected function initModules() {
		// app
		$app = $this->app;

		// get config
		$config = $this->app->config;

		// get twig
		$twig = $app->twig;

		// auth
		$auth = function() {
        		if (!(isset($_SESSION['authentificated']) && $_SESSION['authentificated'] === true)) {
				$app = \Slim\Slim::getInstance();
				$app->flash('error', 'Login required');
				$app->redirect('./login');
			}
		};

		// for each module
		foreach($config->modules as $module => $enabled) {
			// check if module is enabled
			if(!$enabled)
				continue;

			// module route dir
			$routeDir = $config->base->module_path . $module . "/route/";

			// check if folder exists
			if(is_dir($routeDir)) {
				foreach (glob($routeDir . "/*.php") as $filename) {
					require_once($filename);
				}
			}

			// module view dir
			$viewDir = $config->base->module_path . $module . "/view/";

			// check if folder exists
			if(is_dir($viewDir)) {
				// add module view path
				$twig->getLoader()->addPath($viewDir, $module);
			}
		}
	}
}
