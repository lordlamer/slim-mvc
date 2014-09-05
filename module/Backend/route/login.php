<?php

/**
 * show login
 */
$app->get('/admin/login', function () use ($app) {
	//echo "Hello Login";
	echo $app->twig->render("@Backend/login.html");
});

/**
 * post login
 */
$app->post('/login', function () use ($app) {
	$authUser = $app->request->post('user');
	$authPass = $app->request->post('password');

	// authentificate
	if($authUser !== null && $authPass !== null && $authUser == "admin" &&  $authPass == "admin")
		$_SESSION['authentificated'] = true;

	if (isset($_SESSION['authentificated']) && $_SESSION['authentificated'] === true)
		$app->redirect('.');
	else
		$app->redirect('./login');
});