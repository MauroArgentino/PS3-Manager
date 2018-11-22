<?php

    require_once 'mysql_conf.php';

    require_once 'trim_text.php';

    // LOADING SELECTION DEFAULT

    $selector = isset( $_REQUEST[ 'order' ] ) ? intval( $_REQUEST[ 'order' ] ) : 0;

    /// NUMBER OF RECORDS

    $numres = isset( $_REQUEST[ 'numres' ] ) ? intval( $_REQUEST[ 'numres' ] ) : 0;

    if ( $numres == '1000' )
    {
        $limit_str = 'LIMIT 1000';
    }
    else if ( empty( $numres ) )
    {
        $numres = '30';

        $limit_str = 'LIMIT ' . $numres;
    }
    else
    {
        $limit_str = 'LIMIT ' . $numres;
    }

    //file_put_contents( 'selector.txt', $selector );

    $statement = 'SELECT id, name, isoname, covername, numplayed FROM games ';

    if ( $selector == 'score' )
    {
        $statement = 'SELECT m.id,m.name,m.isoname,m.covername,m.numplayed FROM games AS m JOIN game_details as p ON p.id = m.id ORDER BY p.score DESC ' . $limit_str;
    }

    if ( $selector == 'numplayed' )
    {
        $statement_ext = 'ORDER BY numplayed DESC ' . $limit_str;

        $statement = $statement . $statement_ext;
    }

    if ( $selector == 'lastplayed' )
    {
        $statement_ext = 'ORDER BY lastplayed DESC ' . $limit_str;

        $statement = $statement . $statement_ext;
    }

    if ( $selector == 'name' )
    {
        $statement_ext = 'ORDER BY name ' . $limit_str;

        $statement = $statement . $statement_ext;
    }

    if ( $selector == 'dateadded' )
    {
        $statement = 'SELECT id, name, isoname, covername, numplayed FROM games ORDER BY dateadded DESC ' . $limit_str;
    }

    if ( $selector == 'rel_date' )
    {
        $statement = 'SELECT m.id,m.name,m.isoname,m.covername,m.numplayed FROM games AS m JOIN game_details as p ON p.id = m.id ORDER BY p.rel_date DESC ' . $limit_str;
    }

    if ( $selector == 'neverplayed' )
    {
        $statement = "SELECT m.id,m.name,m.isoname,m.covername,m.numplayed FROM games AS m JOIN game_details as p ON p.id = m.id where m.numplayed='0' ORDER by p.score DESC " . $limit_str;
    }

    if ( $selector == 'random' )
    {
        $statement = 'SELECT * from ( SELECT m.id,m.name,m.isoname,m.covername,m.numplayed,p.score FROM games AS m JOIN game_details as p ON p.id = m.id ORDER BY rand() ' . $limit_str . ' ) T1 ORDER by score DESC';
    }

    if ( ! $selector )
    {
        $statement = 'SELECT id, name, isoname, covername, numplayed FROM games ORDER BY lastplayed DESC ' . $limit_str;
    }

    if ( ! $database = new mysqli( DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME ) )
    {
        die ( $database->connect_errno . ' - ' . $database->connect_error );
    }

    $result = $database->query( $statement ) or die( $database->error );

    $game_entry = '';

    if ( $result->num_rows > 0 )
    {
        while ( $obj = $result->fetch_object() )
        {
            $numplayed = $obj->numplayed;

            $raw_name = $obj->name;

            $id = $obj->id;

            $gamename = preg_replace( '~\[(.+?)\]~', '', $raw_name );

            $gamename = str_replace( '_', ' ', $gamename );

            $isoname = $raw_name . '.iso';

            $covername = $raw_name . '.jpg';

            $cover = '<br><br>';

            if ( file_exists( 'covers/' . $covername ) )
            {
                if ( $numplayed == 0 )
                {
                    $cover = '<img alt="COVER" class="example-image" id="covers_search" src="covers/' . $covername . '">';
                }
                else
                {
                    $cover = '<img alt="COVER" class="example-image badgex" id="covers_search" src="covers/' . $covername . '">';
                }
            }
            else
            {
                $cover = '<img alt="COVER" class="example-image" id="covers_search" src="covers/noimage_DE.jpg">';
            }

            $gamename = trim_text( $gamename, 30 );

            if ( $numplayed == 0 )
            {
                $game_entry .= '<div class="col-md-4 col-xs-4 gallery-grid"><a id="example-image" href="games.php?game=' . $id . '">' . $cover . '<div class="game-name"><b>' . $gamename . '</b></div></a></div>';
            }
            else
            {
                $game_entry .= '<div class="col-md-4 col-xs-4 gallery-grid"><a id="example-image" href="games.php?game=' . $id . '">' . $cover . '<div class="game-name"><b>' . $gamename . '</b></div><span class="badgex">' . $numplayed . '</span></a></div>';
            }
        }
    }

?>