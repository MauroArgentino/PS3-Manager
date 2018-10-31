<?php

// MYSQL Server Details

$mysql_host = 'localhost';

$mysql_user = 'root';

$mysql_password = '';

$mysql_db = 'ps3-games'; //<---- change this only if you want to call the database in a different way

defined( 'DB_USER' ) or define( 'DB_USER', $mysql_user );

defined( 'DB_PASSWORD' ) or define( 'DB_PASSWORD', $mysql_password );

defined( 'DB_SERVER' ) or define( 'DB_SERVER', $mysql_host );

defined( 'DB_NAME' ) or define( 'DB_NAME', $mysql_db );

?>