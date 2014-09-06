<?php

$app->get('/admin/logout', $authBE, function () use ($app) {
	$_SESSION['backend_authentificated'] = false;

	if (isset($_COOKIE[session_name()])) {
		setcookie(session_name(), '', time()-42000, '/');
	}

	session_destroy();

	$app->redirect('.');
});