<?php
// Procura self::metodo() e STCMS_Classe::metodo() sem definição correspondente.
$dir = dirname( __DIR__ ) . '/wordpress/wp-content/plugins/studio-tabi-cms/includes';
$definidos = array();
$arquivos = glob( $dir . '/*.php' );
$arquivos[] = dirname( $dir ) . '/studio-tabi-cms.php';

foreach ( $arquivos as $f ) {
	$src = file_get_contents( $f );
	if ( preg_match( '/class\s+(\w+)/', $src, $c ) ) {
		$classe = $c[1];
		if ( preg_match_all( '/function\s+(\w+)\s*\(/', $src, $m ) ) {
			foreach ( $m[1] as $nome ) { $definidos[ $classe ][ $nome ] = true; }
		}
	}
	if ( preg_match_all( '/^function\s+(\w+)\s*\(/m', $src, $m ) ) {
		foreach ( $m[1] as $nome ) { $definidos['__global'][ $nome ] = true; }
	}
}

$problemas = 0;
foreach ( $arquivos as $f ) {
	$src = file_get_contents( $f );
	$classe = preg_match( '/class\s+(\w+)/', $src, $c ) ? $c[1] : '';
	foreach ( explode( "\n", $src ) as $i => $linha ) {
		if ( preg_match_all( '/(self|static|(STCMS_\w+))::(\w+)\s*\(/', $linha, $m, PREG_SET_ORDER ) ) {
			foreach ( $m as $uso ) {
				$alvo  = ( 'self' === $uso[1] || 'static' === $uso[1] ) ? $classe : $uso[2];
				$metodo = $uso[3];
				if ( $alvo && ! isset( $definidos[ $alvo ][ $metodo ] ) ) {
					printf( "  ÓRFÃO  %s::%s()  em %s:%d\n", $alvo, $metodo, basename( $f ), $i + 1 );
					$problemas++;
				}
			}
		}
		if ( preg_match_all( '/(?<![\w:$>])(stcms_\w+)\s*\(/', $linha, $m ) ) {
			foreach ( $m[1] as $fn ) {
				if ( ! isset( $definidos['__global'][ $fn ] ) && ! function_exists( $fn ) ) {
					printf( "  ÓRFÃO  %s()  em %s:%d\n", $fn, basename( $f ), $i + 1 );
					$problemas++;
				}
			}
		}
	}
}
echo $problemas ? "\n{$problemas} chamada(s) sem definição\n" : "nenhuma chamada órfã\n";
exit( $problemas ? 1 : 0 );
