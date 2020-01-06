<?php

    require_once 'mysql_conf.php';

    $name = isset( $_REQUEST[ 'name' ] ) ? htmlspecialchars( $_REQUEST[ 'name' ] ) : null;

    if ( ! $database = new mysqli( DATABASE_HOSTNAME, DATABASE_USERNAME, DATABASE_PASSWORD, DATABASE_NAME ) )
    {
        die( $database->connect_errno . ' - ' . $database->connect_error );
    }

    $name = str_replace( '.iso', '', $name );

    $statement = "SELECT id, numplayed FROM games WHERE name='" . $name . "'";

    $result = $database->query( $statement ) or die( $database->error );

    if ( $result->num_rows > 0 )
    {
        while ( $obj = $result->fetch_object() )
        {
            $numplayed = $obj->numplayed + 1;

            $id = $obj->id;
        }
    }

    // Write file with the current mounted game ID

    @ file_put_contents( 'mounted_id.txt', $id ) or die ( 'Error: ' . basename( __FILE__ ) . ':' . __LINE__ );

    $statement = 'UPDATE games SET numplayed=' . $numplayed . ',lastplayed=CURRENT_TIMESTAMP WHERE id=' . $id;

    $result = $database->query( $statement ) or die( $database->error );

?>
