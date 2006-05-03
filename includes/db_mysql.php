<?php /* $Id: db_mysql.php,v 1.6 2006/04/28 14:52:03 mytto Exp $ */
/*
	Based on Leo West's (west_leo@yahooREMOVEME.com):
	lib.DB
	Database abstract layer
	-----------------------
	MYSQL VERSION
	-----------------------
	A generic database layer providing a set of low to middle level functions
	originally written for WEBO project, see webo source for "real life" usages
*/

function db_connect( $dbid = "std", $host="localhost", $dbname, $user="root", $passwd="", $port="3306", $persist=false ) {
  global $links_db;
  global $dbChronos;
  if(!isset($links_db[$dbid])) {
    function_exists( "mysql_connect" )
      or  die( "FATAL ERROR: MySQL support not available.  Please check your configuration." );

	  if ($persist) {
      $links_db[$dbid] = mysql_pconnect( "$host:$port", $user, $passwd )
        or die( "FATAL ERROR: Connection to database server failed" );
    } else {
      $links_db[$dbid] = mysql_connect( "$host:$port", $user, $passwd )
        or die( "FATAL ERROR: Connection to database server failed" );
    }

    if ($dbname) {
	    mysql_select_db( $dbname, $links_db[$dbid] )
        or die( "FATAL ERROR: Database not found ($dbname)" );
    } else {
      die( "FATAL ERROR: Database name not supplied<br />(connection to database server succesful)" );
    }

    $dbChronos[$dbid] = new Chronometer;
  }
}

function db_error($dbid = "std") {
  global $links_db;
  if(!isset($links_db[$dbid]))
    die( "FATAL ERROR: link to $dbid not found." );
	return mysql_error($links_db[$dbid]);
}

function db_errno($dbid = "std") {
  global $links_db;
  if(!isset($links_db[$dbid]))
    die( "FATAL ERROR: link to $dbid not found." );
	return mysql_errno($links_db[$dbid]);
}

function db_insert_id($dbid = "std") {
  global $links_db;
  if(!isset($links_db[$dbid]))
    die( "FATAL ERROR: link to $dbid not found." );
	return mysql_insert_id($links_db[$dbid]);
}

function db_exec($sql, $dbid = "std") {
  global $dbChronos;
  global $links_db;
  if(!isset($links_db[$dbid]))
    die( "FATAL ERROR: link to $dbid not found." );

  $dbChronos[$dbid]->start();
	$cur = mysql_query( $sql, $links_db[$dbid] );
  $dbChronos[$dbid]->stop();

	if( !$cur ) {
		return false;
	}
  
	return $cur;
}

function db_free_result( $cur ) {
	mysql_free_result( $cur );
}

function db_num_rows( $qid ) {
	return mysql_num_rows( $qid );
}

function db_fetch_row( $cur ) {
	return mysql_fetch_row( $cur );
}

function db_fetch_assoc( $cur ) {
	return mysql_fetch_assoc( $cur );
}

function db_fetch_array( $cur  ) {
	return mysql_fetch_array( $cur );
}

function db_fetch_object( $cur  ) {
	return mysql_fetch_object( $cur );
}

function db_escape( $str ) {
	return mysql_escape_string( $str );
}

function db_version($dbid = "std") {
  global $links_db;
  if(!isset($links_db[$dbid]))
    die( "FATAL ERROR: link to $dbid not found." );
	if( ($cur = mysql_query( "SELECT VERSION()",  $links_db[$dbid])) ) {
		$row =  mysql_fetch_row( $cur );
		mysql_free_result( $cur );
		return $row[0];
	} else {
		return 0;
	}
}

function db_unix2dateTime( $time ) {
	// converts a unix time stamp to the default date format
	return $time > 0 ? date("Y-m-d H:i:s", $time) : null;
}

function db_dateTime2unix( $time ) {
	if ($time == "0000-00-00 00:00:00") {
		return -1;
	}
	if( ! preg_match( "/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})(.?)$/", $time, $a ) ) {
		return -1;
	} else {
		return mktime( $a[4], $a[5], $a[6], $a[2], $a[3], $a[1] );
	}
}
?>