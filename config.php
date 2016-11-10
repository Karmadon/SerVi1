<?php
// config.php

		$dbHostname = 'localhost'; // Unlikely to require changing
	$dbName = 'cmp'; // Modify these...
	$dbUser = 'root'; // ...variables according
	$dbPassword = 'mysql'; // ...to your installation
	$appNameFull = "Computer Masters Plus 2.61"; // ...and preference

	setlocale(LC_ALL, 'uk_UK.UTF-8');
	//mb_internal_encoding("UTF-8"); // ENCODING
	//mb_http_input("UTF-8");
	//mb_http_output("UTF-8");
	date_default_timezone_set('Europe/Kiev');

