<?php
/**
 * Router para o servidor embutido do PHP (php -S) servir o WordPress do
 * diretório runtime/wordpress, incluindo permalinks bonitos e a REST API
 * em /wp-json/*.
 *
 * Uso:  php -S 127.0.0.1:8080 scripts/wp-router.php
 */
$root = getenv( 'WP_DIR' );
if ( ! $root ) {
	$root = dirname( __DIR__ ) . '/runtime/wordpress';
}
$root = rtrim( $root, '/' );

$path = parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );

// Servir arquivos estáticos existentes (css, js, imagens, /wp-admin/*.php, etc.)
$file = realpath( $root . $path );
if ( '/' !== $path && $file && 0 === strpos( $file, realpath( $root ) ) && is_file( $file ) ) {
	if ( '.php' === substr( $file, -4 ) ) {
		chdir( dirname( $file ) );
		require $file;
		return true;
	}
	return false; // deixa o servidor embutido entregar o arquivo estático
}

// Caso contrário, roteia tudo para o index.php do WordPress.
chdir( $root );
require $root . '/index.php';
