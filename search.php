<?php

    require_once 'config.php';

    require_once 'trim_text.php';

    /// NUMBER OF RECORDS

    $numres = isset( $_REQUEST[ 'numres' ] ) ? intval( $_REQUEST[ 'numres' ] ) : 0;

    if ( $_REQUEST[ 'numres' ] == '0' )
    {
        $limit_str = '';
    }
    else if ( empty( $_REQUEST[ 'numres' ] ) )
    {
        $limit_str = '';

        $numres = '0';
    }
    else
    {
        $limit_str = 'LIMIT ' . $numres;
    }

    /// ORDER

    $order = isset( $_REQUEST[ 'order' ] ) ? htmlspecialchars( $_REQUEST[ 'order' ] ) : 'lastplayed';

    if ( ! $database = new mysqli( DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME ) )
    {
        die( $database->connect_errno . ' - ' . $database->connect_error );
    }

    $arr = array();

    if ( ! empty( $_REQUEST[ 'keywords' ] ) )
    {
        $keywords = $database->real_escape_string( $_REQUEST[ 'keywords' ] );

        $statement = "SELECT m.id, m.name, m.isoname, m.covername, m.lastplayed, m.numplayed FROM games AS m JOIN game_details as p ON p.id = m.id WHERE m.name LIKE '%" . $keywords . "%' OR p.category LIKE '%" . $keywords . "%' OR p.tags LIKE '%" . $keywords . "%' ORDER BY p.score DESC " . $limit_str;

        $result = $database->query( $statement ) or die( $databasei->error );

        if ( $result->num_rows > 0 )
        {
            $data_rows = $result->num_rows;

            while ( $obj = $result->fetch_object() )
            {
                $raw_name = $obj->name;

                $lastplayed = $obj->lastplayed;

                $numplayed = $obj->numplayed;

                $id = $obj->id;

                $gamename = preg_replace( '~\[(.+?)\]~', '', $raw_name );

                $gamename = str_replace( '_', ' ', $gamename );

                $gamename = trim_text( $gamename, 18 );

                // Checking if the game has been played before

                if ( $lastplayed == '0000-00-00 00:00:00' )
                {
                    $played = 0;
                }
                else
                {
                    $played = 1;
                }

                $isoname = $raw_name . '.iso';

                $covername = $raw_name . '.jpg';

                $text_rows = $data_rows;

                $arr[] = array( 'id' => $id, 'name' => $gamename, 'isoname' => $isoname, 'covername'=> $covername, 'rescount'=> $text_rows, 'played'=> $played, 'lastplayed'=> $lastplayed, 'numplayed'=> $numplayed );
            }

            echo json_encode( $arr );
        }
        else
        {
            //$data_rows = 'No results found.';

            $data_rows = 0;

            $arr[] = array( 'rescount'=> $data_rows );

            echo json_encode( $arr );
        }
    }
    else
    {
        $statement = 'SELECT id, name, isoname, covername FROM games ORDER BY ' . $order . ' DESC ' . $limit_str;

        $result = $database->query( $statement ) or die( $databasei->error );

        if ( $result->num_rows > 0 )
        {
            $data_rows = $result->num_rows;

            while ( $row = $result->fetch_object() )
            {
                $raw_name = $row->name;

                $id = $row->id;

                $gamename = preg_replace( '~\[(.+?)\]~', '', $raw_name );

                $gamename = str_replace( '_', ' ', $gamename );

                // CHECKING IF TITLE IS TOO LONG FOR THE HTML

                $gamename = trim_text( $gamename, 18 );

                $isoname = $raw_name . '.iso';

                $covername = $raw_name . '.jpg';

                $text_rows = $data_rows;

                $arr[] = array( 'id' => $id, 'name' => $gamename, 'isoname' => $isoname, 'covername'=> $covername, 'rescount'=> $text_rows );
            }

            echo json_encode( $arr );
        }
    }

?>