<?php

$app->get('/admin/', $auth, function () use ($app) {
	//echo "Hello Admin";
	echo $app->twig->render("@Backend/dashboard.html");
});