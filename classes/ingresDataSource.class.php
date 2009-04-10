<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CIngresDataSource extends CSQLDataSource {
  private $nextLimit = null;
  
  function connect($host, $name, $user, $pass) {
    if (!function_exists( "ingres_connect" )) {
      trigger_error("FATAL ERROR: Ingres support not available.  Please check your configuration.", E_USER_ERROR);
      return;
    }
	    
    if ("localhost" != $host) { 
      trigger_error("FATAL ERROR: Ingres server host has to be 'localhost'", E_USER_ERROR);
      return;
    }
    
    if (!$name) { 
      trigger_error("FATAL ERROR: Ingres driver has to select a specific database on 'localhost'", E_USER_ERROR);
      return;
    }
    
    if (null == $this->link = ingres_connect($name, $user, $pass)) { 
      trigger_error("FATAL ERROR: Connection to Ingres server failed", E_USER_ERROR);
      return;
    }
            
    return $this->link;
  }  
    
  function renameTable($old, $new) {
    // No known implementation yet
    return false;
  }
  
  function loadTable($table) {
    $query = "SELECT * FROM iitables".
      "\nWHERE table_name = %";
            
    $values = array (
      $table,
    );
    
    return $this->loadResult($this->prepare($query, $values));
  }


  function loadTables($table = null) {
    $query = "SELECT * FROM iitables".
      "\nWHERE table_name = %";
            
    $values = array (
      "$table%",
    );
    
    return $this->loadColumn($this->prepare($query, $values));
  }
  function loadField($table, $field) {
    $query = "SELECT column_name FROM iiocolumns".
      "\nWHERE table_name = %".
      "\nAND column_name = %";
            
    $values = array (
      $table,
      $field
    );    
    
    return $this->loadResult($this->prepare($query, $values));
  }   

  function error() {
    return ingres_error($this->link);
  }

  function errno() {
    return ingres_errno($this->link);
  }

  function insertId() {
    // No such implementation
    return -1;
  }

  function query($query) {
    // Extract back quotes
    $query = str_replace("`", "", $query);

    // Handle limits
    preg_match("/LIMIT 0,[\s]*([\d]+)/i", $query, $matches);
    $this->nextLimit = @$matches[1];
    $query = preg_replace("/(LIMIT 0,[\s]*[\d]+)/i", "", $query);
    
    $ret = ingres_query($query, $this->link);
    
    // Simulate autocommit because ingres_autocommit() is unavailable
    if ($ret && preg_match("/^(INSERT|UPDATE|DELETE)/i", $query)) {
      ingres_commit($this->link);
    }
    
    return $ret;
  }
  
  function freeResult($result) {
    // No such implementation
    return;
  }

  function numRows($result) {
    // Uses the link
    return ingres_num_rows($this->link);
  }

  function affectedRows() {
    // No such implementation
    return -1;
  }

  private function _fetchArray($mode) {
    // Simulates limit
    if ($this->nextLimit-- === 0) {
      return;
    }
    
   // Uses the link
   return ingres_fetch_array($mode, $this->link);
  }
  
  function fetchRow($result) {
    return $this->_fetchArray(INGRES_NUM);
  }

  function fetchArray($result) {
    return $this->_fetchArray(INGRES_BOTH);
  }

  function fetchAssoc($result) {
    return $this->_fetchArray(INGRES_ASSOC);
  }

  function fetchObject($result) {
    // Simulates limit
    if ($this->nextLimit-- == 0) {
      return;
    }
    
    // Uses the link
    return ingres_fetch_object(INGRES_ASSOC, $this->link);
  }

  function escape($value) {
    return str_replace("'", " ", $value);
  }

  function version() {
    // No such implementation
    return "";
  }
  
  function prepareLike($value) {
    return "LIKE '$value'";
  }
  
  function queriesForDSN($user, $pass, $base) {
    return array(); 
  }
}

?>