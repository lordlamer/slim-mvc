<?php

$app->get('/admin/', $authBE, function () use ($app) {
	//echo "Hello Admin";
	echo $app->twig->render("@Backend/dashboard.html");
});