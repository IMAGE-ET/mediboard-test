<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class COracleDataSource extends CSQLDataSource {
  private $_queries = array();
    
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
    
    // Date formats
    //$this->exec("ALTER SESSION SET NLS_DATE_FORMAT = 'YYYY-MM-DD'");
    $this->exec("ALTER SESSION SET NLS_TIMESTAMP_FORMAT = 'YYYY-MM-DD HH24:MI:SS'");
    $this->exec("ALTER SESSION SET NLS_TIME_FORMAT = 'HH24:MI:SS'");
    $this->exec("ALTER SESSION SET NLS_COMP = LINGUISTIC");
    $this->exec("ALTER SESSION SET NLS_SORT = BINARY_AI"); // accent-insensitive and case-insensitive binary sort

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
    $err = oci_error($this->link);
    return $err['message']." (Query: {$err['sqltext']}, offset: {$err['offset']})";
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
    
    if (CSQLDataSource::$trace) {
      $this->_queries[$stid] = $query;
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
    if (CSQLDataSource::$trace) {
      $t = microtime(true);
    }
    
    $assoc = $this->readLOB(oci_fetch_assoc($result));
    
    if (CSQLDataSource::$trace) {
      $t = (microtime(true) - $t) * 1000;
      mbTrace("$t ms", @$this->_queries[$result]);
    }
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

  function loadHashList($query) {
    $cur = $this->exec($query);
    $cur or CApp::rip();
    
    $ret = oci_fetch_all($cur, $rows, 0, -1, OCI_FETCHSTATEMENT_BY_ROW + OCI_NUM);
    
    $hashlist = array();
    foreach($rows as $hash) {
      $hashlist[$hash[0]] = $hash[1];
    }
    
    $this->freeResult($cur);
    return $hashlist;
  }

  function loadHashAssoc($query) {
    $cur = $this->exec($query);
    $cur or CApp::rip();
    
    $ret = oci_fetch_all($cur, $rows, 0, -1, OCI_FETCHSTATEMENT_BY_ROW + OCI_ASSOC);
    
    $hashlist = array();
    foreach($rows as $hash) {
      $key = reset($hash);
      $hashlist[$key] = $hash;
    }
    
    $this->freeResult($cur);
    return $hashlist;
  }

  function loadList($query, $maxrows = null) {
    if (null == $result = $this->exec($query)) {
      CAppUI::setMsg($this->error(), UI_MSG_ERROR);
      return false;
    }
    
    $ret = oci_fetch_all($result, $list, 0, $maxrows, OCI_FETCHSTATEMENT_BY_ROW + OCI_ASSOC);
    
    $this->freeResult($result);
    return $list;
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