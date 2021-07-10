<?php

function xml_signer_autoload( $classname )
{
	$prefix = "lyquidity\\xmldsig\\";
	if ( strpos( $classname, $prefix ) !== 0 ) return false;
	$filename = str_replace( $prefix, '', $classname );
	require_once  __DIR__ . "/src/$filename.php";
}

spl_autoload_register( 'xml_signer_autoload' );
