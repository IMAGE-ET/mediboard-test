<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class COracleDataSource extends CSQLDataSource {
		
  function connect($host, $name, $user, $pass) {
    if (!function_exists( "oci_connect" )) {
      trigger_error( "FATAL ERROR: Oracle support not available.  Please check your configuration.", E_USER_ERROR );
      return;
    }
	    
    if (false === $this->link = oci_connect($user, $pass, "$host/$name")) {
      $error = $this->error();
      trigger_error( "FATAL ERROR: Connection to Oracle database '$host/$name' failed.\n".$error['message'], E_USER_ERROR );
      return;
    }

    return $this->link;
  }  
    
  function renameTable($old, $new) {
    $query = "ALTER TABLE `$old` RENAME TO `$new`";
    return $this->exec($query);
  }
  
  function loadTable($table) {
    $query = $this->prepare("SHOW TABLES LIKE %", $table);
    return $this->loadResult($query);
  }

  function loadTables($table = "") {
    $query = $this->prepare("SHOW TABLES LIKE %", "$table%");
    return $this->loadColumn($query);
  }
  
  function loadField($table, $field) {
    $query = $this->prepare("SHOW COLUMNS FROM `$table` LIKE %", $field);
    return $this->loadResult($query);
  }   

  function error() {
    return oci_error($this->link);
  }

  function errno() {
    $error = $this->error();
    if ($error === false) return null;
    return $error["code"];
  }

  function insertId() {
    //return mysql_insert_id($this->link);
  }

  function query($query) {
    $stid = oci_parse($this->link, $query);
    oci_execute($stid);
    return $stid;
  }

  function freeResult($result) {
    oci_free_statement($result);
  }

  function numRows($result) {
    return oci_num_rows($result);
  }

  function affectedRows() {
    //return mysql_affected_rows($this->link);
  }

  function fetchRow($result) {
	  return oci_fetch_row($result);
  }

  function fetchAssoc($result) {
    return oci_fetch_assoc($result);
  }

  function fetchArray($result) {
    return oci_fetch_array($result);
  }

  function fetchObject($result, $class_name = null, $params = array()) {
    /*if (empty($class_name))
      return mysql_fetch_object($result);
      
    if (empty($params))
      return mysql_fetch_object($result, $class_name);
    
    return mysql_fetch_object($result, $class_name, $params);*/
  }

  function escape($value) {
    return strtr($value, array(
      "'" => "''",
      '"' => '\"',
    ));
  }
  
  function prepareLike($value) {
    $value = preg_replace('`\\\\`', '\\\\\\', $value);
    return $this->prepare("LIKE %", $value);
  }

  function version() {
    return oci_server_version($this->link);
  }
  
  function queriesForDSN($user, $pass, $base) {
    $queries = array();
    $host = "localhost";
    
    // Create database
    $queries["create-db"] = "CREATE DATABASE `$base` ;";

    // Create user with global permissions
    $queries["global-privileges"] = 
      "GRANT USAGE
        ON * . * 
        TO '$user'@'$host'
        IDENTIFIED BY '$pass';";
      
    // Grant user with database permissions
    $queries["base-privileges"] = 
      "GRANT ALL PRIVILEGES
        ON `$base` . *
        TO '$user'@'$host';";
    
    return $queries;
  }
}

?>