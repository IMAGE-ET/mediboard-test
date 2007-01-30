<?php /* $Id$ */
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


global $db_trace;
$db_trace = null;

function db_connect( $dbid = "std", $host="localhost", $dbname, $user="root", $passwd="", $port="3306", $persist=false ) {
  global $links_db;
  global $dbChronos;
  if(!isset($links_db[$dbid])) {
    if(!function_exists( "mysql_connect" ))
      trigger_error( "FATAL ERROR: MySQL support not available.  Please check your configuration.", E_USER_ERROR );

	  if ($persist) {
      if(!($links_db[$dbid] = mysql_pconnect( "$host:$port", $user, $passwd )))
        trigger_error( "FATAL ERROR: Connection to database server failed", E_USER_ERROR );
    } else {
      if(!($links_db[$dbid] = mysql_connect( "$host:$port", $user, $passwd )))
        trigger_error( "FATAL ERROR: Connection to database server failed", E_USER_ERROR );
    }

    if ($dbname) {
	    if(!mysql_select_db( $dbname, $links_db[$dbid] ))
        trigger_error( "FATAL ERROR: Database not found ($dbname)", E_USER_ERROR );
    } else {
      trigger_error( "FATAL ERROR: Database name not supplied<br />(connection to database server succesful)", E_USER_ERROR );
    }

    $dbChronos[$dbid] = new Chronometer;
  }
}

/**
 * Escapes up to nine values for SQL queries
 * => db_prepare("INSERT INTO table_name VALUES (%)", $value);
 * => db_prepare("INSERT INTO table_name VALUES (%1, %2)", $value1, $value2);
 */
function db_prepare($sql) {
  $values = func_get_args();
  array_shift($values);
  $trans = array();
  for ($i = 0; $i < count($values); $i++) {
    $escaped = db_escape($values[$i]);
    $quoted = "'$escaped'";
    if ($i == 0) {
      $trans["%"] = $quoted;
    }
    
    $key = $i+1;
    $trans["%$key"] = $quoted;
  }
  
  return strtr($sql, $trans);
}

/**
 * Prepares an IN where clause with a given array of values
 * Prepares a standard = where clause when alternate value is supplied
 */
function db_prepare_in($values, $alternate = null) {
  if ($alternate) {
    return "= '$alternate'";
  }
  
  if (!count($values)) {
    return "IS NULL AND 0";
  }
  
  $str = join($values, ", ");
  return "IN ($str)";
}

function db_error($dbid = "std") {
  global $links_db;
  if(!isset($links_db[$dbid]))
    trigger_error( "FATAL ERROR: link to $dbid not found.", E_USER_ERROR );
	return mysql_error($links_db[$dbid]);
}

function db_errno($dbid = "std") {
  global $links_db;
  if(!isset($links_db[$dbid]))
   trigger_error( "FATAL ERROR: link to $dbid not found.", E_USER_ERROR );
	return mysql_errno($links_db[$dbid]);
}

function db_insert_id($dbid = "std") {
  global $links_db;
  if(!isset($links_db[$dbid]))
    trigger_error( "FATAL ERROR: link to $dbid not found.", E_USER_ERROR );
	return mysql_insert_id($links_db[$dbid]);
}

function db_exec($sql, $dbid = "std") {
  global $dbChronos, $links_db, $db_trace;
  if(!isset($links_db[$dbid]))
    trigger_error( "FATAL ERROR: link to $dbid not found.", E_USER_ERROR );

  $dbChronos[$dbid]->start();
	$cur = mysql_query( $sql, $links_db[$dbid] );
  $dbChronos[$dbid]->stop();

  if ($db_trace) {
    trigger_error("Exécution SQL : $sql", E_USER_NOTICE);
  }

	if (!$cur) {
    trigger_error("Exécution SQL : $sql", E_USER_NOTICE);
    trigger_error("Erreur SQL : ".db_error(), E_USER_WARNING);
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

function db_affected_rows($dbid = "std" ) {
  return mysql_affected_rows(db_link($dbid));
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
    trigger_error( "FATAL ERROR: link to $dbid not found.", E_USER_ERROR );
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