<?php

function xml_signer_autoload( $classname )
{
	$prefix = "lyquidity\\xmldsig\\";
	if ( strpos( $classname, $prefix ) !== 0 ) return false;
	$filename = str_replace( $prefix, '', $classname );
	$filename = str_replace('\\', '/', $filename);
	if ( ! file_exists( __DIR__ . "/src/$filename.php" ) )
	{
		$classes = array( 'SigningCertificate' => 'xml/XmlClasses' );
		if ( ! isset( $classes[ $filename ] ) ) return false;
		$filename = $classes[ $filename ];
		if ( ! file_exists( __DIR__ . "/src/$filename.php" ) ) return false;
	}
	require_once __DIR__ . "/src/$filename.php";
}

spl_autoload_register( 'xml_signer_autoload' );
