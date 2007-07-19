<?php

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @version $Revision: $
 *  @author Alexis Granger
 */


class CMySQLDataSource extends CSQLDataSource {
		
  function connect($dsn = "std", $dbhost = "localhost", $dbname, $dbuser = "root", $dbpass = "", $dbport = "3306", $dbpersist = false) {
     if (!function_exists( "mysql_connect" )) {
       trigger_error( "FATAL ERROR: MySQL support not available.  Please check your configuration.", E_USER_ERROR );
       die;
     }
	    
     if ($dbpersist) {
       if (null == $this->link = mysql_pconnect( "$dbhost:$dbport", $dbuser, $dbpass )) { 
         trigger_error( "FATAL ERROR: Connection to database server failed", E_USER_ERROR );
         die;
       } 
     }
     else {
       if (null == $this->link = mysql_connect( "$dbhost:$dbport", $dbuser, $dbpass )) { 
         trigger_error( "FATAL ERROR: Connection to database server failed", E_USER_ERROR );
         die;
       }
     }
     
    if ($dbname) {
      if (!mysql_select_db($dbname, $this->link)) {
        trigger_error( "FATAL ERROR: Database not found ($dbname)", E_USER_ERROR );
        die;
      }
    }

    return $this->link;
  }

  
  /**
  * Document::insertArray() 
  *
  * { Description }
  *
  * @param [type] $verbose
  */
  function insertArray($table, &$hash, $verbose = false) {
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
      $values[] = "'" . escape($v) . "'";
    }
    $sql = sprintf($fmtsql, implode(",", $fields) ,  implode(",", $values));

    ($verbose) && print "$sql<br />\n";

    if (!$this->exec($sql)) {
      return false;
    }
    $id = $this->insertId();
    return true;
  }
  
  
  /**
  * Document::updateArray() 
  *
  * { Description }
  *
  * @param [type] $verbose
  */
  function updateArray($table, &$hash, $keyName, $verbose = false) {
    global $dPconfig;
    if($dPconfig["readonly"]) {
      return false;
    }
    $fmtsql = "UPDATE $table SET %s WHERE %s";
    foreach ($hash as $k => $v) {
      if(is_array($v) or is_object($v) or $k[0] == "_") // internal or NA field
        continue;

      if($k == $keyName) { // PK not to be updated
        $where = "$keyName='" . $this->escape($v) . "'";
        continue;
      }
      if ($v == "") {
        $val = "NULL";
      } else {
        $val = "'" . $this->escape($v) . "'";
      }
      $tmp[] = "$k=$val";
    }
    $sql = sprintf($fmtsql, implode(",", $tmp) , $where);
    ($verbose) && print "$sql<br />\n";
    
    $ret = $this->exec($sql);
    return $ret;
  }
  
  
  /**
  * Document::delete()  
  *
  * { Description } 
  *
  */
  function delete($table, $keyName, $keyValue) {
    global $dPconfig;
    if($dPconfig["readonly"]) {
      return false;
    }
    $keyName = $this->escape($keyName);
    $keyValue = $this->escape($keyValue);
    $sql = "DELETE FROM $table WHERE $keyName='$keyValue'";
    
    $ret = $this->exec($sql);
    return $ret;
  }
  
  

  /**
  * Document::insertObject() 
  *
  * { Description } 
  *
  * @param [type] $keyName
  * @param [type] $verbose
  */
  function insertObject($table, &$object, $keyName = null, $verbose = false) {
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
      $values[] = "'" . $this->escape($v) . "'";
    }
    $sql = sprintf($fmtsql, implode(",", $fields) ,  implode(",", $values));
    ($verbose) && print "$sql<br />\n";

   
    if (!$this->exec($sql)) {
      return false;
    }
    $id = $this->insertId();
    ($verbose) && print "id=[$id]<br />\n";
    if ($keyName && $id)
      $object->$keyName = $id;
    return true;
  }
  
  
  
  /**
  * Document::updateObject() 
  *
  * { Description }
  *
  * @param [type] $updateNulls
  */
  function updateObject($table, &$object, $keyName) {
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
        $where = "`$keyName`='" . $this->escape($v) . "'";
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
        $val = "'" . $this->escape($v) . "'";
      }
      $tmp[] = "`$k`=$val";
    }
    $sql = sprintf($fmtsql, implode(",", $tmp) , $where);
 
    return $this->exec($sql);
  }
  
  
  function loadTable($table) {
    $query = $this->prepare("SHOW TABLES LIKE %", $table);
    return $this->loadResult($query);
  }

  function loadField($table, $field) {
    $query = $this->prepare("SHOW COLUMNS FROM `$table` LIKE %", $field);
    return $this->loadResult($query);
  } 

  /**
  * Prepares an IN where clause with a given array of values
  * Prepares a standard = where clause when alternate value is supplied
  */

  // à remonter
  function prepareIn($values, $alternate = null) {
    if ($alternate) {
      return "= '$alternate'";
    }
    if (!count($values)) {
      return "IS NULL AND 0";
    }
    $str = join($values, ", ");
    return "IN ($str)";
  }
  

  function error() {
    return mysql_error($this->link);
  }

  function errno() {
    return mysql_errno($this->link);
  }

  function insertId() {
    return mysql_insert_id($this->link);
  }

  function exec($sql) {
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
      trigger_error("Erreur SQL : ".$this->error(), E_USER_WARNING);
	  return false;
	}
  
	return $cur;
  }


  
  function freeResult( $cur ) {
	mysql_free_result( $cur );
  }

  function numRows( $qid ) {
	return mysql_num_rows( $qid );
  }

  function affectedRows() {
    return mysql_affected_rows($this->link);
  }

  function fetchRow( $cur ) {
	return mysql_fetch_row( $cur );
  }

  function fetchAssoc( $cur ) {
    return mysql_fetch_assoc( $cur );
  }

  function fetchArray( $cur  ) {
	return mysql_fetch_array( $cur );
  }

  function fetchObject( $cur  ) {
	return mysql_fetch_object( $cur );
  }

  function escape( $str ) {
	return mysql_escape_string( $str );
  }

  function version() {
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