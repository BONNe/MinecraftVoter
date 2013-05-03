<?php
defined('IN_APP') or exit;

if( $config['minecraft.economy.type'] == 'essentials' )
{
	$CommandPrefix = 'eco give';
}
else if( $config['minecraft.economy.type'] == 'iconomy' )
{
	$CommandPrefix = 'money give';
}
else if( $config['minecraft.economy.type'] == 'boseconomy' )
{
	$CommandPrefix = 'econ add';
}
else
{
	echo '<div class="alert alert-error">$config[\'minecraft.economy.type\'] parametrs ir nepareizs, Tevis izvēlētais noteikti neder.</div>';
	exit;
}

if( !isset( $rcon_connection_failed ) AND !empty( $username )  )
{
	
	if( $config[ 'minecraft.ingame.notifications' ] )
	{
		//if( preg_match( "/^[a-zA-Z0-9_?]+$/",  $username ) )
		//{
			$Rcon->Command( "say " . $username . " nobalsoja par mums un sanema bonusus. ", true );
		//}
	}
	
	$Rcon->Command( $CommandPrefix . " " . $username . " " . $credits , true );
	$Rcon->Command( "msg " . $username . " Tu sanjeemi "  . $credits . " krediitus", true );
		
	if( isset( $config['random_prizes'] ) AND isset( $config['links'][ $target ][ 'prizes' ] ) AND $config['links'][ $target ][ 'prizes' ] )
	{
		$prize = $config['random_prizes'][ array_rand( $config['random_prizes'], 1 ) ];
			
		$Rcon->Command( "give " . $username . " " . $prize['item_id'] . " " . $prize['count'] . " " . $prize['enchants'], true );
		$Rcon->Command( "msg " . $username . " Tu sanjeemi " . $prize['title'], true );
	}

	

}