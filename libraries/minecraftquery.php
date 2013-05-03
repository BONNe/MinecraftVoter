<?php
/*********************************************************************************\
|   ___ _   _ _ __   ___ ___  _ __ ___                                            |
|  / __| | | | '_ \ / __/ _ \| '__/ _ \                                           |
|  \__ \ |_| | | | | (_| (_) | | |  __/                                           |
|  |___/\__,_|_| |_|\___\___/|_|  \___|                                           |
| ------------------------------------------------------------------------------- |
| Software by:                Janis Blaus (Glorificus @ http://www.suncore.lv/)   |
|                              & kr1ska                                           |
| Copyright 2010-2012 by:     SUNCORE SIA                                         |
| Support, News, Updates at:  http://www.suncore.lv/                              |
|  (Visit www.suncore.lv for details and information about this script)           |
| ------------------------------------------------------------------------------- |
| WARNING:                                                                        |
| Dual licensed under the MIT and GPL licenses:                                   |
|   http://www.opensource.org/licenses/mit-license.php                            |
|   http://www.gnu.org/licenses/gpl.html                                          |
|                                                                                 |
\*********************************************************************************/

/*
Installation:
	@ server.properties:
		enable-query=true
		query.port=25565
*/

class MinecraftQuery
{
	public $ip;
	public $port;
	
	function __construct( $ip = '127.0.0.1', $port = 25565 )
	{
		$this->ip   = $ip;
		$this->port = $port;
	}
	
	function is_server_online()
	{
		$fp = @fsockopen( $this->ip, $this->port, $errno, $errstr, 2 );
		
		if ( !$fp )
		{
			return false;
		}
		
		return true;
	}
	
	function get_mc_info( $timeout = 1 )
	{
		function get_data($socket, $request, $additional = "") // writes & gets data
		{
			$write = "\xFE\xFD" . $request . "\x01\x02\x03\x04" . $additional ;
			$write_length = strlen( $write);
			$length = fwrite( $socket, $write, $write_length );
			
			if( ($length === $write_length) === false )
			{
				return false;
			}
			
			$response = fread($socket, 2048);
			
			return empty($response) === true ? false : $response;
		};

		$fp = fsockopen( 'udp://' . $this->ip, $this->port );
		socket_set_timeout( $fp, $timeout );
		
		if( empty($fp) === true ) return false; // 'connection failed'
		
		$challenge_request = get_data( $fp, "\x09" );
		if( ! $challenge_request )
		{	
			//  'challenge request failed' 
			return false;
		}
		
		$challenge = pack( 'N', substr($challenge_request, 5) );
		$response = get_data( $fp, "\x00", $challenge . "\x01\x02\x03\x04" );
		if( ! $response ) return false; // 'info request failed'
		
		$data = explode("\x00", $response);
		array_shift( $data );
		$array_raw = array_slice($data, 0, 22); // Retrieve the necessary array values
		$data_chunks = array_chunk($array_raw, 2);
		$server = array();
		
		foreach( $data_chunks as $row )
		{
			$server[ trim(current($row)) ] = end( $row );
		}

		$server['plugins_raw'] = $server['plugins'];
		$arr = explode(': ', $server['plugins']);
		if( is_array($arr) === true )
		{
			$server['server'] = current($arr); // Server Mod
			$server['plugins'] = explode('; ', end($arr) ); // Activated server mod plugins
		}
		else
		{
			$server['server'] = $server['plugins'] = "";
		}
		
		$server['players_list'] = array_slice($data, 25, $server[ 'numplayers' ] );
		$server['connect_port'] = $this->port;
		
		return (object)$server;
	}
}