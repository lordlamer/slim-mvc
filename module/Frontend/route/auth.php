<?php

// auth
$auth = function() {
	if (!(isset($_SESSION['authentificated']) && $_SESSION['authentificated'] === true)) {
		$app = \Slim\Slim::getInstance();
		$app->flash('error', 'Login required');
		$app->redirect('./login');
	}
};