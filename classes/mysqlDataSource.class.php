<?php

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @version $Revision: $
 *  @author Alexis Granger
 */


class CMySQLDataSource extends CSQLDataSource {
		
  function connect($host, $name, $user, $pass) {
    if (!function_exists( "mysql_connect" )) {
      trigger_error( "FATAL ERROR: MySQL support not available.  Please check your configuration.", E_USER_ERROR );
      die;
    }
	    
    if (null == $this->link = mysql_connect($host, $user, $pass)) { 
      trigger_error( "FATAL ERROR: Connection to MySQL server failed", E_USER_ERROR );
      die;
    }
     
    if ($name) {
      if (!mysql_select_db($name, $this->link)) {
        trigger_error( "FATAL ERROR: Database not found ($name)", E_USER_ERROR );
        die;
      }
    }

    return $this->link;
  }  
    
  function loadTable($table) {
    $query = $this->prepare("SHOW TABLES LIKE %", $table);
    return $this->loadResult($query);
  }

  function loadField($table, $field) {
    $query = $this->prepare("SHOW COLUMNS FROM `$table` LIKE %", $field);
    return $this->loadResult($query);
  }   

  function error() {
    return mysql_error($this->link);
  }

  function errno() {
    return mysql_error($this->link);
  }

  function insertId() {
    return mysql_insert_id($this->link);
  }

  function query($query) {
    return mysql_query($query, $this->link);
  }

  function freeResult($result) {
    mysql_free_result($result);
  }

  function numRows($result) {
    return mysql_num_rows($result);
  }

  function affectedRows() {
    return mysql_affected_rows($this->link);
  }

  function fetchRow($result) {
	  return mysql_fetch_row($result);
  }

  function fetchAssoc($result) {
    return mysql_fetch_assoc($result);
  }

  function fetchArray($result) {
    return mysql_fetch_array($result);
  }

  function fetchObject($result) {
    return mysql_fetch_object($result);
  }

  function escape($value) {
    return mysql_escape_string($value);
  }

  function version() {
    return $this->loadResult("SELECT VERSION()");
  }
  
  function queriesForDSN($user, $pass, $base) {
    $queries = array();
    $host = "localhost";
    
    // Create database
    $queries["create-db"] = "CREATE DATABASE `$base` ;";

    // Create user with global permissions
    $queries["global-privileges"] = "GRANT USAGE" .
      "\nON * . * " .
      "\nTO '$user'@'$host'" .
      "\nIDENTIFIED BY '$pass';";
      
    // Grant user with database permissions
    $queries["base-privileges"] = "GRANT ALL PRIVILEGES" .
      "\nON `$base` . *" .
      "\nTO '$user'@'$host';";
    
    return $queries;
  }
}

?>