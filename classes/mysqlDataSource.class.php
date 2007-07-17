<?php

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @version $Revision: $
 *  @author Alexis Granger
 */


class CMySQLDataSource extends CSQLDataSource {
		
  function connect($dsn = "std", $dbhost = "localhost", $dbname, $dbuser = "root", $dbpass = "", $dbport = "3306", $dbpersist = false) {
	
  	if(!isset($this->link)) {
      if(!function_exists( "mysql_connect" )) 
        trigger_error( "FATAL ERROR: MySQL support not available.  Please check your configuration.", E_USER_ERROR );
 	    
      if ($dbpersist) {
        if(!($this->link = mysql_pconnect( "$dbhost:$dbport", $dbuser, $dbpass ))) 
          trigger_error( "FATAL ERROR: Connection to database server failed", E_USER_ERROR );
      } 
      else {
        if(!($this->link = mysql_connect( "$dbhost:$dbport", $dbuser, $dbpass ))) 
          trigger_error( "FATAL ERROR: Connection to database server failed", E_USER_ERROR );
      }
      if ($dbname) {
	    if(!mysql_select_db( $dbname, $this->link )) trigger_error( "FATAL ERROR: Database not found ($dbname)", E_USER_ERROR );
      } else {
        trigger_error( "FATAL ERROR: Database name not supplied<br />(connection to database server succesful)", E_USER_ERROR );
      }
      $this->chrono = new Chronometer;
    }
    return $this->link;
  }

  
  /**
  * Document::db_insertArray() 
  *
  * { Description }
  *
  * @param [type] $verbose
  */
  function db_insertArray($table, &$hash, $verbose = false) {
    //global $dPconfig;
    if($dPconfig["readonly"]) {
      return false;
    }
    $fmtsql = "insert into $table (%s) values(%s) ";
    foreach ($hash as $k => $v) {
      if (is_array($v) or is_object($v) or $v === null) {
        continue;
      }
      $fields[] = $k;
      $values[] = "'" . db_escape($v) . "'";
    }
    $sql = sprintf($fmtsql, implode(",", $fields) ,  implode(",", $values));

    ($verbose) && print "$sql<br />\n";

    if (!$this->db_exec($sql)) {
      return false;
    }
    $id = $this->db_insert_id();
    return true;
  }
  
  
  /**
  * Document::db_updateArray() 
  *
  * { Description }
  *
  * @param [type] $verbose
  */
  function db_updateArray($table, &$hash, $keyName, $verbose = false) {
    global $dPconfig;
    if($dPconfig["readonly"]) {
      return false;
    }
    $fmtsql = "UPDATE $table SET %s WHERE %s";
    foreach ($hash as $k => $v) {
      if(is_array($v) or is_object($v) or $k[0] == "_") // internal or NA field
        continue;

      if($k == $keyName) { // PK not to be updated
        $where = "$keyName='" . $this->db_escape($v) . "'";
        continue;
      }
      if ($v == "") {
        $val = "NULL";
      } else {
        $val = "'" . $this->db_escape($v) . "'";
      }
      $tmp[] = "$k=$val";
    }
    $sql = sprintf($fmtsql, implode(",", $tmp) , $where);
    ($verbose) && print "$sql<br />\n";
    
    $ret = $this->db_exec($sql);
    return $ret;
  }
  
  
  /**
  * Document::db_delete()  
  *
  * { Description } 
  *
  */
  function db_delete($table, $keyName, $keyValue) {
    global $dPconfig;
    if($dPconfig["readonly"]) {
      return false;
    }
    $keyName = $this->db_escape($keyName);
    $keyValue = $this->db_escape($keyValue);
    $sql = "DELETE FROM $table WHERE $keyName='$keyValue'";
    
    $ret = $this->db_exec($sql);
    return $ret;
  }
  
  

  /**
  * Document::db_insertObject() 
  *
  * { Description } 
  *
  * @param [type] $keyName
  * @param [type] $verbose
  */
  function db_insertObject($table, &$object, $keyName = null, $verbose = false) {
    global $dPconfig;
    if($dPconfig["readonly"]) {
      return false;
    }
    $fmtsql = "INSERT INTO $table (%s) VALUES (%s) ";
    foreach (get_object_vars($object) as $k => $v) {
      if (is_array($v) or is_object($v) or $v === null) {
        continue;
      }
      if ($k[0] == "_") { // internal field
        continue;
      }
      $v = trim($v);
      if($v === "" && $k != $keyName) { // empty field
        continue;
      }
      $fields[] = "`$k`";
      $values[] = "'" . $this->db_escape($v) . "'";
    }
    $sql = sprintf($fmtsql, implode(",", $fields) ,  implode(",", $values));
    ($verbose) && print "$sql<br />\n";

   
    if (!$this->db_exec($sql)) {
      return false;
    }
    $id = $this->db_insert_id();
    ($verbose) && print "id=[$id]<br />\n";
    if ($keyName && $id)
      $object->$keyName = $id;
    return true;
  }
  
  
  
  /**
  * Document::db_updateObject() 
  *
  * { Description }
  *
  * @param [type] $updateNulls
  */
  function db_updateObject($table, &$object, $keyName) {
    global $dPconfig;
    if($dPconfig["readonly"]) {
      return false;
    }
    $fmtsql = "UPDATE $table SET %s WHERE %s";
    $tmp = array();
    foreach (get_object_vars($object) as $k => $v) {
      if(is_array($v) or is_object($v) or $k[0] == "_") { // internal or NA field
        continue;
      }
      if($k == $keyName) { // PK not to be updated
        $where = "`$keyName`='" . $this->db_escape($v) . "'";
        continue;
      }
      if ($v === null) {
        continue;
      }
      $v = trim($v);
      if($v === "") {
        // Tries to nullify empty values but won't fail if not possible
        $val = "NULL";
      } else {
        $val = "'" . $this->db_escape($v) . "'";
      }
      $tmp[] = "`$k`=$val";
    }
    $sql = sprintf($fmtsql, implode(",", $tmp) , $where);
 
    return $this->db_exec($sql);
  }
  
  
  function db_loadTable($table, $dsn = "std") {
    $query = $this->db_prepare("SHOW TABLES LIKE %", $table);
    return $this->db_loadResult($query, $dsn);
  }

  function db_loadField($table, $field, $dsn = "std") {
    $query = $this->db_prepare("SHOW COLUMNS FROM `$table` LIKE %", $field);
    return $this->db_loadResult($query, $dsn);
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
  

  function db_error($dsn = "std") {
    if(!isset($this->link))
      trigger_error( "FATAL ERROR: link to $dsn not found.", E_USER_ERROR );
  	return mysql_error($this->link);
  }

  function db_errno($dsn = "std") {
    if(!isset($this->link))
     trigger_error( "FATAL ERROR: link to $dsn not found.", E_USER_ERROR );
   return mysql_errno($this->link);
  }

  function db_insert_id($dsn = "std") {
    if(!isset($this->link))
      trigger_error( "FATAL ERROR: link to $dsn not found.", E_USER_ERROR );
	return mysql_insert_id($this->link);
  }

  function db_exec($sql, $dsn = "std") {
    global $db_trace;
    
    if(!isset($this->link))
      trigger_error( "FATAL ERROR: link to $this->dsn not found.", E_USER_ERROR );
 
      $this->chrono->start();
      $cur = mysql_query( $sql, $this->link );
      $this->chrono->stop();

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

  function db_affected_rows($dsn = "std" ) {
    return mysql_affected_rows(db_link($dsn));
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

  function db_version($dsn = "std") {

    if(!isset($this->link))
      trigger_error( "FATAL ERROR: link to $dsn not found.", E_USER_ERROR );
  	  if( ($cur = mysql_query( "SELECT VERSION()",  $this->link)) ) {
		$row =  mysql_fetch_row( $cur );
		mysql_free_result( $cur );
		return $row[0];
	  } else {
		return 0;
	}
  }
  
}

?>