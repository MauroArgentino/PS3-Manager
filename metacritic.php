<?php

    //error_reporting( E_ERROR | E_PARSE );

    date_default_timezone_set( 'Europe/Amsterdam' );

    include ( 'config.php' );

    define( 'DATABASE_USERNAME', $database_username );

    define( 'DATABASE_PASSWORD', $database_password );

    define( 'DATABASE_HOSTNAME', $database_hostname );

    define( 'DATABASE_NAME', $database_name );

    if ( ! $database = new mysqli( DATABASE_HOSTNAME, DATABASE_USERNAME, DATABASE_PASSWORD, DATABASE_NAME ) )
    {
        die( $database->connect_errno . ' - ' . $database->connect_error );
    }

    $statement = 'SELECT id, name FROM games';

    $result = $database->query( $statement ) or die( $database->error );

    if ( $result->num_rows > 0 )
    {
        $i = 1;

        $count = 0;

        while ( $obj = $result->fetch_object() )
        {
            $name = preg_replace( '~\[(.+?)\]~', '', $obj->name );

            $name = str_replace( '_', ' ', $name );

            $name = str_replace( '-', ' ', $name );

            // CHECK IF ALREADY IN TABLE game_details

            $statement_chk = 'SELECT id FROM game_details WHERE id=' . $obj->id;

            echo $statement_chk . ' ';

            $database_chk = mysqli_connect( $database_hostname, $database_username, $database_password, $database_name );

            $result_chk = $database_chk->query( $statement_chk ) or die( $database->error );

            echo $result_chk->num_rows . "\n\n";

            if ( $result_chk->num_rows > 0 )
            {
                $count = $count+1;

                echo 'ALREADY IN DATABASE game_details ' . $name . ' ID ' . $obj->id . "\n\n";

                continue;
            }

            $id = $obj->id;

            $name = trim( $name );

            echo $name . "\n\n";

            # Ignore Unirest warning if any ( eg . safe mode related )

            error_reporting( E_ERROR | E_PARSE );

            include 'classes/metacritic/metacritic_api-master/metacritic.php';

            $metacritic_api = new MetacriticAPI();

            $response = $metacritic_api->get_metacritic_page( $name );

            $json_reponse = $metacritic_api->get_metacritic_scores( $response );

            $data = json_decode( $json_reponse, TRUE );

            var_dump( $data );

            $category = $data[ 'genres' ];

            $developer = $data[ 'developers' ];

            $developer = str_replace( "'", '', $developer );

            $publisher = $data[ 'publishers' ];

            $publisher = str_replace( "'", '', $publisher );

            $score = $data[ 'metascritic_score' ];

            $rlsdate = str_replace( ',', '', $data[ 'release_date' ] );

            $description = str_replace( "'", ' ', $data[ 'description' ] );

            $statement_ins = "INSERT INTO game_details (id,name,category,developer,publisher,score,rlsdate,description) VALUES ('" . $id . "','" . $name . "','" . $category . "','" . $developer . "','" . $publisher . "','" . $score . "','" . $rlsdate . "','" . $description . "')";

            echo $statement_ins . "\n\n";

            $database_ins = mysqli_connect( $database_hostname, $database_username, $database_password, $database_name );

            if ( mysqli_query( $database_ins, $statement_ins ) )
            {
                echo 'New record created successfully' . "\n\n";
            }
            else
            {
                echo 'Error: ' . $statement_ins . ' ' . mysqli_error( $database_ins ) . "\n\n";
            }

            $i = $i + 1;
        }
    }

?>