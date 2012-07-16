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

class CMySQLiDataSource extends CMySQLDataSource {
  /**
   * @var MySQLi
   */
  var $link;
  
  const ASSOC = 1; // MYSQLI_ASSOC;
  const NUM   = 2; // MYSQLI_NUM;
  const BOTH  = 3; // MYSQLI_BOTH;
    
  function connect($host, $name, $user, $pass) {
    if (!class_exists("MySQLi")) {
      trigger_error( "FATAL ERROR: MySQLi support not available.  Please check your configuration.", E_USER_ERROR );
      return;
    }
    
    $this->link = new MySQLi($host, $user, $pass, $name);
    
    if ($error = $this->link->connect_error) { 
      trigger_error( "FATAL ERROR: Connection to MySQL server failed ($error)", E_USER_ERROR );
      return;
    }
     /*
    if ($name) {
      if ($this->link->select_db($name)) {
        trigger_error( "FATAL ERROR: Database not found ($name)", E_USER_ERROR );
        return;
      }
    }*/

    return $this->link;
  }

  function error() {
    return $this->link->error;
  }

  function errno() {
    return $this->link->errno;
  }

  function insertId() {
    return $this->link->insert_id;
  }

  function query($query) {
    return $this->link->query($query);
  }

  function freeResult($result) {
    $result->free();
  }

  function numRows($result) {
    return $result->num_rows;
  }

  function affectedRows() {
    return $this->link->affected_rows;
  }
  
  function fetchRow($result) {
    return $result->fetch_row();
  }

  function fetchAssoc($result) {
    return $result->fetch_assoc();
  }

  function fetchArray($result) {
    return $result->fetch_array();
  }

  function fetchObject($result, $class_name = null, $params = array()) {
    if (empty($class_name))
      return $result->fetch_object();
      
    if (empty($params))
      return $result->fetch_object($class_name);
    
    return $result->fetch_object($class_name, $params);
  }
  
  /*
  protected function fetchAll(mysqli_result $result, $result_type = self::NUM) {
    return $result->fetch_all($result_type);
  }

  function loadHashList($query) {
    $cur = $this->exec($query);
    $cur or CApp::rip();
    
    $rows = $this->fetchAll($cur, self::NUM);
    
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
    
    $rows = $this->fetchAll($cur, self::ASSOC);
    
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
    
    $list = $this->fetchAll($result, self::ASSOC);
    
    if ($maxrows) {
      $list = array_slice($list, 0, $maxrows);
    }
    
    $this->freeResult($result);
    return $list;
  }
  */

  function escape($value) {
    return $this->link->escape_string($value);
  }

  function version() {
    return $this->link->server_info;
  }
}

?>