<?php

$app->get('/logout', $auth, function () use ($app) {
	$_SESSION['authentificated'] = false;

	if (isset($_COOKIE[session_name()])) {
		setcookie(session_name(), '', time()-42000, '/');
	}

	session_destroy();

	$app->redirect('.');
});