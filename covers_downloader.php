<?php

    // $url_cover_hoster = 'http://art.gametdb.com/ps3/coverHQ/US/BLES00001.jpg';

    $url_cover_hoster = 'http://localhost/';

    $list_of_covers = file_get_contents( $url_cover_hoster . 'list_of_covers.php' ) or die ( 'Can not load the list of covers.' );

    $list_of_covers = json_decode( $list_of_covers, true ) or die ( 'A string in JSON format is expected, but can not be processed.' );

    if ( array_key_exists( 'error', $list_of_covers ) )
    {
        die ( 'Error ' . $list_of_covers[ 'error' ] );
    }

    require_once 'config.php';

    error_reporting( E_ERROR | E_PARSE );

    date_default_timezone_set( 'Europe/Amsterdam' );

    $path = $ps3_folder;

    $directory_iterator = new RecursiveDirectoryIterator( $path, FilesystemIterator::SKIP_DOTS );

    $directory_iterator = new RecursiveIteratorIterator( $directory_iterator );

    $directory_iterator = new RegexIterator( $directory_iterator, '/.+\.iso/', RegexIterator::MATCH );

    foreach ( $directory_iterator as $iterator_object )
    {
        $file_name = $iterator_object->getFilename();

        // preg_match_all( '/\[([^\]]*)\]/', $file_name, $matches );

        if ( preg_match( '/\[([\w]{4}[\d]{5})\]/', $file_name, $match ) !== 1 )
        {
            continue;
        }

        // echo gmstrftime( '%F' ) . ' - ' . $match[ 1 ] . ' - File ' . $path . DIRECTORY_SEPARATOR . $file_name . PHP_EOL;
        echo gmstrftime( '%F' ) . ' - ' . $match[ 1 ] . ' - File ' . $path . DIRECTORY_SEPARATOR . $file_name . '<br>';

        $path_local_cover = $path . DIRECTORY_SEPARATOR . str_replace( '.iso', '', $file_name ) . '.jpg';

        if ( file_exists( $path_local_cover ) )
        {
            // echo gmstrftime( '%F' ) . ' - ' . $match[ 1 ] . ' -- File exists ' . $path_local_cover . PHP_EOL;
            echo gmstrftime( '%F' ) . ' - ' . $match[ 1 ] . ' -- File exists ' . $path_local_cover . '<br>';
        }
        else
        {
            if ( array_key_exists( $match[ 1 ], $list_of_covers ) )
            {
                $list_of_available_languages_for_a_cover = $list_of_covers[ $match[ 1 ] ];

                $language = in_array( 'EN', $list_of_available_languages_for_a_cover ) ? 'EN' : $list_of_available_languages_for_a_cover[ 0 ];

                $language = in_array( 'DE', $list_of_available_languages_for_a_cover ) ? 'DE' : $language;

                $file_content = file_get_contents( $url_cover_hoster . $language . DIRECTORY_SEPARATOR . $match[ 1 ] . '.jpg' );

                if ( $file_content )
                {
                    // echo gmstrftime( '%F' ) . ' - ' . $match[ 1 ] . ' -- ' . $language . ' -- File is created ' . $path_local_cover . PHP_EOL;
                    echo gmstrftime( '%F' ) . ' - ' . $match[ 1 ] . ' -- ' . $language . ' -- File is created ' . $path_local_cover . '<br>';

                    // if ( file_put_contents( $path_local_cover, $file_content ) )
                    // {
                    //     echo gmstrftime( '%F' ) . ' - ' . $match[ 1 ] . ' -- File is created ' . $path_local_cover . PHP_EOL;
                    // }
                    // else
                    // {
                    //     echo gmstrftime( '%F' ) . ' - ' . $match[ 1 ] . ' -- Can not create file ' . $path_local_cover . PHP_EOL;
                    // }
                }
            }
            else
            {
                // echo  gmstrftime( '%F' ) . ' - ' . $match[ 1 ] . ' -- File not found on the remote server for ' . $match[ 1 ] . PHP_EOL;
                echo  gmstrftime( '%F' ) . ' - ' . $match[ 1 ] . ' -- File not found on the remote server for ' . $match[ 1 ] . '<br>';
            }
        }
    }

?>