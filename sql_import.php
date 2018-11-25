<?php

    require_once 'config.php';

    $database = new mysqli( $database_hostname, $database_username, $database_password, $database_name );

    if ( $database->connect_error )
    {
        die( 'Connection failed: ' . $database->connect_error );
    }

    date_default_timezone_set( 'Europe/Amsterdam' );

    $directory = $ps3_folder;

    $scanned_directory = array_diff( scandir( $directory ), array( '..', '.' ) );

    $x = 0;

    foreach ( $scanned_directory as $value )
    {
        // Renaming Files;

        $newfile =  str_replace( ' ', '_', $value );

        $newfile =  str_replace( "'", '', $newfile );

        $newfile =  str_replace( '&', '', $newfile );

        rename( $directory . '/' . $value, $directory . '/' . $newfile );

        if  ( strpos( $value, 'iso' ) AND strpos( $newfile, 'CONVERSION' ) === false )
        {
            $game_name = str_replace( '.iso', '', $newfile );

            $statement = "SELECT * FROM `games` WHERE `name` = '" . $game_name . "';";

            echo $statement . "\n";

            $result = $database->query( $statement );

            if ( $result->num_rows > 0 )
            {
                echo 'Record ' . $game_name . ' already exists' . "\n\n";
            }
            else
            {
                $statement = "INSERT INTO `games`(`name`, `isoname`, `covername`,`dateadded`) VALUES ('" . $game_name . "','" . $directory . '/' . $newfile . "','" . $directory . '/' . $newfile . ".jpg',CURRENT_TIMESTAMP);";

                echo $statement . "\n";

                if ( $database->query( $statement ) === TRUE )
                {
                    echo $game_name . ' record created successfully.' . "\n\n";
                }
                else
                {
                    echo 'Error: ' . $statement . ' ' . $database->error . "\n\n";
                }
            }
        }
    }

    $database->close();

    // ADDING DETAILS TO game_details

    require_once 'metacritic.php';

?>