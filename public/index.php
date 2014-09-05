<?php

/**
 * PIM
 *
 * PIM Desc
 *
 * @author Frank Habermann <habermann@2im.de>
 * @date 20140617
 * @company Integra Internet Management GmbH
 */

// Define path to project directory
defined('PROJECT_PATH')
    || define('PROJECT_PATH', realpath(dirname(__FILE__) . '/..'));

// use composer autoload
require PROJECT_PATH . '/vendor/autoload.php';

// init app
$app = new \IIM\Loader\Application(PROJECT_PATH . '/config/app.ini');

// run auth app
$app->run();
