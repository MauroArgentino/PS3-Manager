<?php

    include 'config.php';

    $id = isset( $_REQUEST[ 'id' ] ) ? intval( $_REQUEST[ 'id' ] ) : 0;

    $statement = "SELECT name,description FROM game_details WHERE ID='" . $id . "'";

    if ( ! $database = new mysqli( DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME ) )
    {
        die( $database->connect_errno . ' - ' . $database->connect_error );
    }

    $result = $database->query( $statement ) or die( $database->error );

    if ( $result->num_rows > 0 )
    {
        while ( $obj = $result->fetch_object() )
        {

            $game_det = $obj->description;
        }
    }

    if ( strlen( $game_det ) >= 2500 )
    {
        // $game_det = substr( $game_det, 0, 2500 );

        $game_det = substr( $game_det, 0, strrpos( substr( $game_det, 0, 2500 ), ' ' ) );

        $game_det = $game_det . '.';
    }

    // echo "<div style='margin-left: auto; margin-right: auto;'>"

    echo "<p style='font-size: small; color: #337AB7; text-align: justify;text-justify: inter-word;'>" . $game_det . '</p><br><br>';

    echo '<button style="margin-top: 10px; padding-right: 10px; padding-left: 10px; background: #CFE0F1" class="game_details_close">Done</button>';

    // echo '</div>';

?>