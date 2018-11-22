<?php

    require_once 'mysql_conf.php';

    // FUNCTION TO CONVERT TO READABLE FILESIZE

    function FileSizeConvert( $bytes )
    {
        $bytes = floatval( $bytes );

        $arBytes = array( 0 => array( 'UNIT' => 'TB', 'VALUE' => pow( 1024, 4 ) ), 1 => array( 'UNIT' => 'GB', 'VALUE' => pow( 1024, 3 ) ), 2 => array( 'UNIT' => 'MB', 'VALUE' => pow( 1024, 2 ) ), 3 => array( 'UNIT' => 'KB', 'VALUE' => 1024 ), 4 => array( 'UNIT' => 'B', 'VALUE' => 1 ), );

        foreach( $arBytes as $arItem )
        {
            if ( $bytes >= $arItem[ 'VALUE' ] )
            {
                $result = $bytes / $arItem[ 'VALUE' ];

                $result = str_replace( '.', ',', strval( round( $result, 2 ) ) ) . ' ' . $arItem[ 'UNIT' ];

                break;
            }
        }
        return $result;
    }

    // Get games number

    $database = @ new mysqli( DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME );

    if ( $database->connect_errno )
    {
        die( $database->connect_errno . ' - ' . $database->connect_error );
    }

    $statement_count = 'SELECT id FROM games';

    $result_count = $database->query( $statement_count ) or die( $database->error );

    $games_number = $result_count->num_rows;

    // Get never played games number

    $statement_count_np = "SELECT id FROM games where numplayed='0'";

    $result_count_np = $database->query( $statement_count_np ) or die( $database->error );

    $games_number_np = $result_count_np->num_rows;

    // GET SUM SIZE OF ALL GAMES

    $glob_size = file_exists( 'glob_iso_size.txt' ) ? file_get_contents( 'glob_iso_size.txt' ) : null;

    $glob_size = FileSizeConvert( $glob_size );

?>