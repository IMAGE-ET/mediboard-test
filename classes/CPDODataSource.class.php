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

class CPDODataSource extends CSQLDataSource {
  protected $driver_name;
  protected $affected_rows;
  
  /**
   * @var PDO
   */
  var $link;
    
  function connect($host, $name, $user, $pass) {
    if (!class_exists("PDO")) {
      trigger_error( "FATAL ERROR: PDO support not available. Please check your configuration.", E_USER_ERROR );
      return;
    }
    
    $dsn = "$this->driver_name:dbname=$name;host=$host";
    $this->link = new PDO($dsn, $user, $pass);

    return $this->link;
  }

  function error() {
    return $this->link->errorInfo();
  }

  function errno() {
    return $this->link->errorCode();
  }

  function insertId() {
    return $this->link->lastInsertId();
  }

  function query($query) {
    $stmt = $this->link->query($query);
    $this->affected_rows = $stmt->rowCount();
    return $stmt;
  }

  function freeResult($result) {
    //$result->free();
  }

  function numRows($result) {
    return $result->rowCount();
  }

  function affectedRows() {
    return $this->affected_rows;
  }
  
  function foundRows() {
    // No such implementation
    return;
  }
  
  function fetchRow($result) {
    return $result->fetch(PDO::FETCH_NUM);
  }

  function fetchAssoc($result) {
    return $result->fetch(PDO::FETCH_ASSOC);
  }

  function fetchArray($result) {
    return $result->fetch(PDO::FETCH_BOTH);
  }

  function fetchObject($result, $class_name = null, $params = array()) {
    if (empty($class_name))
      return $result->fetchObject();
      
    if (empty($params))
      return $result->fetchObject($class_name);
    
    return $result->fetchObject($class_name, $params);
  }

  function escape($value) {
    //return substr($this->link->quote($value), 1, -1); // remove the quotes around
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
    return $this->link->server_info;
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