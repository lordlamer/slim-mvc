<?php

// auth
$authBE = function() {
	if (!(isset($_SESSION['backend_authentificated']) && $_SESSION['backend_authentificated'] === true)) {
		$app = \Slim\Slim::getInstance();
		$app->flash('error', 'Login required');
		$app->redirect('./login');
	}
};