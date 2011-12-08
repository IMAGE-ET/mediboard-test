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
    // Charset options
    //putenv("NLS_LANGUAGE=French_France.WE8ISO8859P1");
    //putenv("NLS_CHARACTERSET=WE8ISO8859P1");
    //putenv("NLS_NCHAR_CHARACTERSET=WE8ISO8859P1");
    
    if (!function_exists( "oci_connect" )) {
      trigger_error( "FATAL ERROR: Oracle support not available.  Please check your configuration.", E_USER_ERROR );
      return;
    }
      
    if (false === $this->link = oci_connect($user, $pass, "$host/$name", "WE8ISO8859P1")) {
      $error = $this->error();
      trigger_error( "FATAL ERROR: Connection to Oracle database '$host/$name' failed.\n".$error['message'], E_USER_ERROR );
      return;
    }
    
    // Date formats
    $this->exec("ALTER SESSION SET NLS_DATE_FORMAT = 'YYYY-MM-DD'");
    $this->exec("ALTER SESSION SET NLS_TIMESTAMP_FORMAT = 'YYYY-MM-DD HH24:MI:SS'");
    $this->exec("ALTER SESSION SET NLS_TIME_FORMAT = 'HH24:MI:SS'");
    
    //$this->exec("ALTER SESSION SET NLS_CHARACTERSET = 'WE8ISO8859P15'");
    //$this->exec("ALTER SESSION SET NLS_NCHAR_CHARACTERSET = 'WE8ISO8859P15'");

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
    if (!oci_execute($stid)) {
      mbLog($query);
    }
    return $stid;
  }

  function freeResult($result) {
    oci_free_statement($result);
  }

  function numRows($result) {
    return oci_num_rows($result);
  }

  function affectedRows() {
    // No such implementation
    return -1;
  }
  
  function foundRows() {
    // No such implementation
    return;
  }
  
  function getCountSelect($found_rows) {
    return "SELECT COUNT(*) as total";
  }
  
  private function readLOB($hash) {
    if (empty($hash)) {
      return $hash;
    }
    
    foreach($hash as &$value) {
      if (is_a($value, "OCI-Lob")) {
        if ($size = $value->size()) {
          $value = $value->read($size);
        }
        else {
          $value = "";
        }
      }
    }
    
    return $hash;
  }
  
  function fetchRow($result) {
    return $this->readLOB(oci_fetch_row($result));
  }

  function fetchAssoc($result) {
    return $this->readLOB(oci_fetch_assoc($result));
  }

  function fetchArray($result) {
    return $this->readLOB(oci_fetch_array($result));
  }

  function fetchObject($result, $class = null, $params = array()) {
    /** @todo Implement !
    if (empty($class)
      return mysql_fetch_object($result);
      
    if (empty($params))
      return mysql_fetch_object($result, $class);
    
    return mysql_fetch_object($result, $class, $params);
     */
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