<?php

// Database connection for Dockerized MariaDB (see docker-compose.yaml)
return array(
	'connectionString' => 'mysql:host=db;dbname=infotek',
	'emulatePrepare' => true,
	'username' => 'infotek',
	'password' => 'infotek',
	'charset' => 'utf8mb4',
);
