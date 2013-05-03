<?php
//file_put_contents('log.txt',print_r($_GET,true),FILE_APPEND);

$bcv_hosts = array();

$bcv_hosts[ @gethostbyname( 'wos.lv' ) ]       = 'wos';
$bcv_hosts[ @gethostbyname( 'direct.8d.lv' ) ] = '8d';


if( in_array( $_SERVER['REMOTE_ADDR'], array_keys( $bcv_hosts ) ) )
{
	if( isset(  $_GET['user'] ) AND !empty(  $_GET['user'] ) )
	{
		$target  = $bcv_hosts[ $_SERVER[ 'REMOTE_ADDR' ] ];
		$username = $_GET['user'];
		
		if( !defined('IN_APP') )
		{
			define( 'IN_APP', 1 );
			
			include './includes/boot.php';
			include ROOT . '/includes/config.php';
		}
		try
		{
			$Rcon = new MinecraftRcon;
			$Rcon->Connect( $config['rcon.ip'], $config['rcon.port'], $config['rcon.password'], 15 );
		}
		catch( MinecraftRconException $e )
		{
			$rcon_connection_failed = true;
		}
		
		if( !isset($credits))
		{
			$credits  = $config['links'][ $target ][ 'credits' ];
		}
		
		include ROOT . '/includes/bonus.php';
		
		
	}
} 