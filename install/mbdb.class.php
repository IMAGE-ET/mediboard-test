<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage install
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

require_once("DB.php");

class CMbDb {
  var $dsn = array();
  
  var $_db;
  var $_errors = array();
  
  function CMbDb($host, $user, $pass, $base = false, $port = "3306") {
    $this->dsn = array(
      "phptype"  => "mysql",
      "username" => $user,
      "password" => $pass,
      "hostspec" => $host,
      "port"     => $port,
      "database" => $base,
    );
  }
  
  function logError($error, $prefix = "") {
    $prefix .= sprintf("\nErreur # %s\nMessage: %s, \nInfo: %s", $error->code, $error->message, $error->userinfo);
    $this->_errors[] = $prefix;
  }

  function emptyLogs() {
    $this->_errors = array();
  }
  
  function connect() {
    $this->_db =& DB::connect($this->dsn);
    if (PEAR::isError($this->_db)) {
      $this->logError($this->_db, "Problème de connexion");
      return false;
    } 
    
    return true;
  }
      
  function query($query, $params = array()) {
    $res =& $this->_db->query($query, $params);
    if (PEAR::isError($res)) {
      $this->logError($res, "Erreur d'exécution de requête");
      return false;
    }

    return $res;
  }
  
  function getOne($query, $params = array()) {
    $res =& $this->_db->getOne($query, $params);
    if (PEAR::isError($res)) {
      $this->logError($res, "Erreur d'exécution de requête");
      return false;
    }

    return $res;
  }
  
  function queryDump($path) {
   $sqlLines = file($path);
   $query = "";
   foreach($sqlLines as $lineNumber => $sqlLine) {
     $sqlLine = trim($sqlLine);
     if (($sqlLine != "") && (substr($sqlLine, 0, 2) != "--") && (substr($sqlLine, 0, 1) != "#")) {
       $query .= $sqlLine;
       if (preg_match("/;\s*$/", $sqlLine)) {
         $this->query($query);
         $query = "";
       }
     }
   }
  }
}
