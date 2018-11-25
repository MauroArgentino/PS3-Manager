<?php

    error_reporting( E_ALL );

    require_once 'mobiledetect/Mobile_Detect.php';

    $detect = new Mobile_Detect;

    require_once 'config.php';

    // require_once 'classes/debug.php';

    require_once 'get_total_iso_size.php';

    require_once 'init_search.php';

    $directory = $ps3_folder;

    $x = 0;

    $now = date( 'F j, Y, g:i a' );

    // require_once 'check_usb.php';

    // SHUTDOWN CALL

    if ( isset( $_REQUEST[ 'command' ] ) && $_REQUEST[ 'command' ] == 'shutdown' )
    {
        $web_call_gamedata = @ file_get_contents( 'http://' . $ps3_ip . '/shutdown.ps3' ) or die ( 'Error: ' . basename( __FILE__ ) . ':' . __LINE__ );

        // $update_status = @ file_get_contents( 'http://' . $_SERVER[ 'SERVER_NAME' ] . '/ps3_status_checker.php' ) or die ( 'Error: ' . basename( __FILE__ ) . ':' . __LINE__ );

        header( 'Refresh:0; url=index.php' );
    }

    // REBOOT CALL

    if ( isset( $_REQUEST[ 'command' ] ) && $_REQUEST[ 'command' ] == 'reboot' )
    {
        $web_call_gamedata = @ file_get_contents( 'http://' . $ps3_ip . '/reboot.ps3?quick' ) or die ( 'Error: ' . basename( __FILE__ ) . ':' . __LINE__ );

        // $update_status = @ file_get_contents( 'http://' . $_SERVER[ 'SERVER_NAME' ] . '/ps3_status_checker.php' ) or die ( 'Error: ' . basename( __FILE__ ) . ':' . __LINE__ );

        header( 'Refresh:0; url=index.php' );
    }

    // UNMOUNT CALL

    if ( isset( $_REQUEST[ 'command' ] ) && $_REQUEST[ 'command' ] == 'unmount' )
    {
        $statement_call = @ file_get_contents( 'http://' . $_SERVER[ 'SERVER_NAME' ] . '/game_update_timeplay.php?id=' . $id ) or die ( 'Error: ' . basename( __FILE__ ) . ':' . __LINE__ );

        $web_call_gamedata = @ file_get_contents( 'http://' . $ps3_ip . '/mount.ps3/unmount' ) or die ( 'Error: ' . basename( __FILE__ ) . ':' . __LINE__ );

        // $update_status = @ file_get_contents( 'http://' . $_SERVER[ 'SERVER_NAME' ] . '/ps3_status_checker.php' ) or die ( 'Error: ' . basename( __FILE__ ) . ':' . __LINE__ );

        sleep( 3 );

        header( 'Refresh:0; url=index.php' );
    }

    // MOUNT CALL

    $mount = isset( $_REQUEST[ 'mount' ] ) ? htmlspecialchars( $_REQUEST[ 'mount' ] ) : null;

    if ( $mount )
    {
        $statement_call = @ file_get_contents( 'http://' . $_SERVER[ 'SERVER_NAME' ] . '/game_update_sql.php?name=' . $mount ) or die ( 'Error: ' . basename( __FILE__ ) . ':' . __LINE__ );

        $web_call_unmount = @ file_get_contents( 'http://' . $ps3_ip . '/mount.ps3/unmount' ) or die ( 'Error: ' . basename( __FILE__ ) . ':' . __LINE__ );

        $web_call_mount = @ file_get_contents( 'http://' . $ps3_ip . '/mount.ps3/net0/PS3ISO/' . $mount ) or die ( 'Error: ' . basename( __FILE__ ) . ':' . __LINE__ );

        header( 'Refresh:0; url=index.php' );
    }

    // GETTING STATUS HTML FILE FROM ps3_status_output . php

    // CHOOSING HTML FILE ACCORDING TO THE DETECTED DEVICE

    $mobile_page = 0;

    if ( $detect->isMobile() && !$detect->isTablet() )
    {
        // Mobile Devices

        $webpage = @ file_get_contents( 'html_files/mobile.html' ) or die ( 'Error: ' . basename( __FILE__ ) . ':' . __LINE__ );

        $menu_html = @ file_get_contents( 'html_files/menu_mobile.html' ) or die ( 'Error: ' . basename( __FILE__ ) . ':' . __LINE__ );

        $mobile_page = 1;
    }
    else if ( $detect->isTablet() )
    {
        // Any tablet device .

        $webpage = @ file_get_contents( 'html_files/base.html' ) or die ( 'Error: ' . basename( __FILE__ ) . ':' . __LINE__ );

        $menu_html = @ file_get_contents( 'html_files/menu.html' ) or die ( 'Error: ' . basename( __FILE__ ) . ':' . __LINE__ );
    }
    else
    {
        // Desktops

        $webpage = @ file_get_contents( 'html_files/base.html' ) or die ( 'Error: ' . basename( __FILE__ ) . ':' . __LINE__ );

        $menu_html = @ file_get_contents( 'html_files/menu.html' ) or die ( 'Error: ' . basename( __FILE__ ) . ':' . __LINE__ );
    }

    $version_writer = @ file_get_contents( 'js/popups.js' ) or die ( 'Error: ' . basename( __FILE__ ) . ':' . __LINE__ );

    $version_writer = str_replace( '%CURRENT_VERSION%', $app_version, $version_writer );

    @ file_put_contents( 'js/popups.js', $version_writer ) or die ( 'Error: ' . basename( __FILE__ ) . ':' . __LINE__ );

    $popups_control = @ file_get_contents( 'html_files/popups.html' ) or die ( 'Error: ' . basename( __FILE__ ) . ':' . __LINE__ );

    // INJECTING DATA INTO HTML

    $webpage = str_replace( '%POPUPS_CONTROL%', $popups_control, $webpage ); // <--- THIS ONE FOR FIRST

    $webpage = str_replace( '%GAMES_LIST%', $game_entry, $webpage );

    $webpage = str_replace( '%GAMES_NUMBER%', $games_number, $webpage );

    $webpage = str_replace( '%GAMES_NUMBER_NP%', $games_number_np, $webpage );

    $webpage = str_replace( '%GLOB_SIZE%', $glob_size, $webpage );

    $webpage = str_replace( '%NAV_MENU%', $menu_html, $webpage );

    /// LOADING USB GAME DATA STATUS FILE AND CHANGING MENU

    $game_data_status = @ file_get_contents( 'game_data_status.txt' ) or die ( 'Error: ' . basename( __FILE__ ) . ':' . __LINE__ );

    $output_set_gamedata = '<a href="index.php?command=gamedata" onclick="return confirm( \'Change Gamedata Setup ?\' )">' . $game_data_status . '</a>';

    if ( $mobile_page == 1 )
    {
        $output_set_gamedata = '<a class="links" style="color: black; text-decoration: none" onclick="return confirm( \'Change Gamedata Setup ?\' )" href="index.php?command=gamedata">' . $game_data_status . '</a>';
    }

    if ( $game_data_status == 'NO USB DRIVE' )
    {
        $output_set_gamedata = '<a href="#">' . $game_data_status . '</a>';

        if ( $mobile_page == 1 )
        {
            $output_set_gamedata = '<a class="links" style="color: black; text-decoration: none"  href="#">' . $game_data_status . '</a>';
        }
    }

    $webpage = str_replace( '%GAME_DATA_SETTING%', $output_set_gamedata, $webpage );

    //SETTING UP SELECT DROP DOWN

    $selector = isset( $_REQUEST[ 'order' ] ) ? htmlspecialchars( $_REQUEST[ 'order' ] ) : null;

    $numres_select = isset( $_REQUEST[ 'numres' ] ) ? htmlspecialchars( $_REQUEST[ 'numres' ] ) : null;

    require_once 'selector_html.php';

    $head_select = empty( $head_select ) ? null : $head_select;

    $tail_select = empty( $tail_select ) ? null : $tail_select;

    $html_select = $head_select . $html_select . $tail_select;

    $webpage = str_replace( '%SELECT_ORD%', $html_select, $webpage );

    $webpage = str_replace( '%SELECT_NUMRES%', $html_numres, $webpage );

    $webpage = str_replace( '%NUM_RES%', $numres, $webpage );

    $webpage = str_replace( '%PS3_IP%', $ps3_ip, $webpage );

    // RENDERING FINAL HTML PAGE

    echo $webpage;

?>