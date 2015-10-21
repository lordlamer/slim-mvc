<?php

namespace SlimMVC;

/**
 * Application class
 */
class Application
{
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
    public function __construct($modulePath, $modules)
    {
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
    public function run()
    {
        $this->app->run();
    }

    /**
     * boostrap the app
     */
    protected function bootstrap()
    {
        $this->initSlim();
        $this->initModules();
    }

    /**
     * init slim
     */
    protected function initSlim()
    {
	// init di container
	$container = new \Slim\Container;

        // init slim app
        $this->app = new \Slim\App($container);
    }

    /**
     * bootstrap modules
     */
    protected function initModules()
    {
        // app
        $app = $this->app;

	// di container
	$container = $app->getContainer();

        // navigation
        $navigation = array();

        // view paths
        $viewModules = array();

        // loader
        $loader = new \Zend\Loader\StandardAutoloader();

        // for each module
        foreach ($this->modules as $module) {
            require_once($this->modulePath . '/' . $module . "/Module.php");
            $m = "\\$module\\Module";
            $m = new $m;

            // autoloader
            if (method_exists($m, 'getAutoloaderConfig')) {
                $cfg = $m->getAutoloaderConfig();

                // register module namespace
                foreach ($cfg as $key => $value) {
                    $loader->registerNamespace($key, $value);
                }

                // register namespaces
                $loader->register();
            }

            // routes
            if (method_exists($m, 'getRouterConfig')) {
                $cfg = $m->getRouterConfig();

                // load routes
                foreach ($cfg as $value) {
                    foreach (glob($value . "/*.php") as $filename) {
                        require_once($filename);
                    }
                }
            }

            // views
            if (method_exists($m, 'getViewConfig')) {
                $cfg = $m->getViewConfig();

                // get view paths
                foreach ($cfg as $key => $value) {
                    // get module view path
                    // store path in app to register it with template engine
                    $viewModules[$key] = $value;
                }
            }

            // navigation
            if (method_exists($m, 'getNavigationConfig')) {
                                // get navigation config
                $cfg = $m->getNavigationConfig();

                                // merge navigation recursiv
                $navigation = array_merge_recursive($navigation, $cfg);
            }

            // middleware
            if (method_exists($m, 'getMiddlewareConfig')) {
                $cfg = $m->getMiddlewareConfig();

                // load routes
                foreach ($cfg as $value) {
                    foreach (glob($value . "/*.php") as $filename) {
                        require_once($filename);
                    }
                }
            }
        }

        // save view modules
        $container->viewModules = $viewModules;

        // save navigation
        $container->navigation = $navigation;
    }
}
