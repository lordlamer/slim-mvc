<?php

$app->get('/admin/', $authBE, function () use ($app) {
	//echo "Hello Admin";

	$test = new \Backend\Test();

	echo $app->twig->render("@Backend/dashboard.html");
});