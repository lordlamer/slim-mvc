<?php

/**
 * SlimMVC
 *
 * SlimMVC Desc
 *
 * @author Frank Habermann <lordlamer@lordlamer.de>
 * @date 20141223
 */

// Define path to project directory
defined('PROJECT_PATH')
    || define('PROJECT_PATH', realpath(dirname(__FILE__) . '/..'));

// use composer autoload
require PROJECT_PATH . '/vendor/autoload.php';

// init app
$app = new \SlimMVC\Application(PROJECT_PATH . '/config/app.ini');

// run auth app
$app->run();
