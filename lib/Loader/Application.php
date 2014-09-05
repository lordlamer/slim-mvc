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
}
