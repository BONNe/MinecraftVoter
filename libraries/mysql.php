<?php
Abstract class mysql {
   
	private static $log = array();

	public static function q( $q, $data = false )
	{
		if( !$data )
		{
			return self::exec($q);
		}
		
		if( is_array( $data ) )
		{
			return self::exec( vsprintf( $q, array_map( 'mysql::escape', $data ) ) );
		}
		
		return self::exec( sprintf( $q, self::escape( $data ) ) );
		
	}

	public static function get_all( $q, $data = false )
	{
		$res = self::q( $q, $data );
		
		$r = array();
		
		while( $row = mysql_fetch_object( $res ) )
		{
			$r[] = $row;
		}
		
		return $r;
		
	}

	public static function get( $q, $data = false )
	{
		return mysql_fetch_object( self::q( $q, $data ) );
	}
	
	public static function getA( $q, $data = false )
	{
		return mysql_fetch_array( self::q( $q, $data ) );
	}
	
	public static function insert( $table, $data )
	{
		$data = self::parse_data( $data );
		
		self::exec( "INSERT INTO `$table` (" . implode( ', ', array_keys( $data ) ) . ") VALUES (" . implode( ',', array_values( $data ) ) . ")");
		
		return mysql_insert_id();
	}

    public static function update( $table, $data, $where = false, $tail = false )
	{
		$data = self::parse_data( $data );
        $set = array();
		
        foreach( $data as $key => $value )
		{
            $set[] = '`' . $key . '` = ' . $value;
        }
		
        if( $where ) 
		{
            $where = self::parse_data( $where );
			
            $setw = array();
			
            foreach( $where as $key => $value )
			{
                $setw[] = '`' . $key . '` = ' . $value;
            }
        }
       
	    self::exec("UPDATE `$table` SET "  
					. implode( ', ', $set )
					. ( $where ? " WHERE " . implode( ' AND ', $setw ) : "" ) 
					. ( $tail  ? " " . $tail : "" ) );
        
		return mysql_affected_rows();
		
    }
	
	public static function del( $table, $where = false )
	{
		self::exec( "DELETE FROM `$table`" . ( $where ? " WHERE $where" : "" ) );
		
		return mysql_affected_rows();
	}

	public static function escape( $var )
	{
		return self::num( $var ) ? $var : "'" . self::secure( $var ) . "'";
	}
	
	public static function secure( $var )
	{
		return mysql_real_escape_string ( $var, self::connect() );
	}

	public static function debug()
	{
		$time = $queries = 0;
		$html = '';
		$html .= '<p style="margin:0; margin: 10px 10px 0 10px;text-align: left; color: #000;background-color:#fff; border: 1px solid #ccc; padding: 8px; font: 11px/1em Courier New;">';
		foreach(self::$log as $q)
		{
			$html .= '<span style="font: 11px/1em Courier New;'.( $q['time'] >= 0.2 ? ' color: #936;' : '' ) . '">'.number_format($q['time'], 4, '.', '').'</span>: '.self::highlight_query(htmlspecialchars($q['query'])).'<br />';
			
			$time+= $q['time'];
			++$queries;
		}
		$html .= '
			<p>
			<p style="margin: 0 10px 10px 10px; text-align: left;color: #000;border: 1px solid #ccc; border-top: 0; background-color:#fff;padding: 8px; font: 11px/1.1em tahoma;">
				Driver: <strong>MySQL</strong><br />
				Total queries: <strong>'.$queries.'</strong><br />
				Total time: <strong>'.number_format($time, 4, '.', '').'</strong>
			</p>
		';
		
		return $html;
		
	}

	private static function num( $var )
	{
		return (!preg_match('/^\-?\d+(\.\d+)?$/D', $var) || preg_match('/^0\d+$/D', $var)) ? false : true;
	}

	private static function parse_data( $data )
	{
		$r = array();
		
		foreach( $data as $key => $value )
		{
			if( $key[0] == '~' )
			{
				$r[substr($key, 1)] = $value;
			}
			else if( $key[0] == '#' )
			{
				$r[ substr($key, 1) ] = $value;
			}
			else
			{
				$r[ $key ] = self::escape( $value );
			}
		}
		
		return $r;
   }

	private static function exec( $q )
	{
		$start = self::time();
		
		$res = mysql_query( $q, self::connect() );
		
		$end = self::time();
		
		self::$log[] = array(
			'time' => $end-$start,
			'query' => $q
		);
		
		if( $res )
		{
			return $res;
		}
		else
		{
			trigger_error("<br /><strong>Query failed:</strong> $q<br /><strong>mysql said:</strong> ". mysql_error());
			return false;
		}
	}

	private static function time()
	{
		list($usec, $sec) = explode(" ", microtime());
		return (float)$usec + (float)$sec;
	}

	private static function highlight_query($query)
	{
		$highlight = array(
			"/(FROM|JOIN|SET|INTO)\s+([^\s]+)/i" => '$1 <span style="font: 11px/1em Courier New; color: #936;">$2</span>',
			"/(WHERE|AND|OR| IN|ORDER BY|GROUP BY|ON|SELECT|AS|,)\s+([^\s,]+)/i" => '$1 <span style="font: 11px/1em Courier New; color: #369;">$2</span>'
		);
		$query = preg_replace(array_keys($highlight), array_values($highlight), $query);
		return $query;
   }
   
   private static function connect()
   {
		$con = mysql_connect(MYSQL_HOST,MYSQL_USER,MYSQL_PASS);
		
		if( !$con )
		{
			return self::trigger_error("<br /><strong>Connection failed!</strong> ");
		}
		
		mysql_select_db( MYSQL_BASE, $con );

		return $con;
   }
   
   private static function trigger_error( $mes )
   {
		die( $mes );
   }
   
}