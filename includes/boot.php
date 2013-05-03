<?php
define( 'VERSION', 1 );
define( 'SUNCORE', 1 );
define( 'ROOT', realpath( dirname( __FILE__ ). '/../' ) );


define( 'URL', 'http' . ( ( !empty( $_SERVER['HTTPS'] ) ) ? 's' : '' ) . '://' . $_SERVER['SERVER_NAME'] . '/' . basename( ROOT ) );

function __autoload( $class_name )
{
	$class_name = strtolower( $class_name );

	require ROOT . "/libraries/" . $class_name . ".php";
}