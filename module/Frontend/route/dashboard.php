<?php

$app->get('/', $auth, function () use ($app) {
	//echo "Hello Frontend";
	echo $app->twig->render("@Frontend/dashboard.html");
});