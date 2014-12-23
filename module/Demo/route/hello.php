<?php

$app->get('/', function () use ($app) {
	$msg = new \Demo\Test();
	echo $app->twig->render("@Demo/example.html", array(
		'message' => $msg->getMessage(),
	));
});