<?php

$order = isset( $_REQUEST[ 'order' ] ) ? htmlspecialchars( $_REQUEST[ 'order' ] ) : null;

@ file_put_contents( 'selector.txt', $order ) or die ( 'Error: ' . basename( __FILE__ ) . ':' . __LINE__ );

?>