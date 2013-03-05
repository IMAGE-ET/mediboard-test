<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage classes
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

class CMySQLDataSource extends CSQLDataSource {
    
  function connect($host, $name, $user, $pass) {
    if (!function_exists("mysql_connect")) {
      trigger_error("FATAL ERROR: MySQL support not available.  Please check your configuration.", E_USER_ERROR);
      return;
    }
      
    if (null == $this->link = mysql_connect($host, $user, $pass, true)) { 
      trigger_error("FATAL ERROR: Connection to MySQL server failed", E_USER_ERROR);
      return;
    }
     
    if ($name) {
      if (!mysql_select_db($name, $this->link)) {
        trigger_error("FATAL ERROR: Database not found ($name)", E_USER_ERROR);
        return;
      }
    }

    return $this->link;
  }  
    
  function renameTable($old, $new) {
    $query = "RENAME TABLE `$old` TO `$new`";
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

  function fetchObject($result, $class = null, $params = array()) {
    if (empty($class)) {
      return mysql_fetch_object($result);
    }
      
    if (empty($params)) {
      return mysql_fetch_object($result, $class);
    }
    
    return mysql_fetch_object($result, $class, $params);
  }

  function escape($value) {
    return mysql_real_escape_string($value, $this->link);
  }
  
  function prepareLike($value, $diacritics_insensitive = false) {
    $value = preg_replace('`\\\\`', '\\\\\\', $value);

    $query = "LIKE %";
    if ($diacritics_insensitive) {
      $query = "LIKE CONVERT(% USING utf8)";
    }

    return $this->prepare($query, $value);
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
  
  function foundRows() {
    return $this->loadResult("SELECT FOUND_ROWS()");
  }
}
