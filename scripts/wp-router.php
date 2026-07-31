<?php
$root = getenv( 'WP_DIR' );
if ( ! $root ) {
	$root = dirname( __DIR__ ) . '/runtime/wordpress';
}
$root = rtrim( $root, '/' );

$path = parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );

$file = realpath( $root . $path );
if ( '/' !== $path && $file && 0 === strpos( $file, realpath( $root ) ) && is_file( $file ) ) {
	if ( '.php' === substr( $file, -4 ) ) {
		chdir( dirname( $file ) );
		require $file;
		return true;
	}
	return false;
}

chdir( $root );
require $root . '/index.php';
