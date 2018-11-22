<?php

$order = isset( $_REQUEST[ 'order' ] ) ? htmlspecialchars( $_REQUEST[ 'order' ] ) : null;

file_put_contents( 'selector.txt', $order );

?>