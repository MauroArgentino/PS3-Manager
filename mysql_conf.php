<?php

// MYSQL Server Details

$database_host = 'localhost';

$database_user = 'root';

$database_password = '';

$database_db = 'ps3-games'; //<---- change this only if you want to call the database in a different way

defined( 'DB_USER' ) or define( 'DB_USER', $database_user );

defined( 'DB_PASSWORD' ) or define( 'DB_PASSWORD', $database_password );

defined( 'DB_SERVER' ) or define( 'DB_SERVER', $database_host );

defined( 'DB_NAME' ) or define( 'DB_NAME', $database_db );

?>