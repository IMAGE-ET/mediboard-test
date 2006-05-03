<?php /* $Id: dbconnection.php,v 1.2 2006/05/02 16:31:03 mytto Exp $ */

/**
* @package Mediboard
* @subpackage install
* @version $Revision: 1.2 $
* @author Thomas Despoix
*/

/**
 * To be removed from source control
 */

//class CDBConnection {
//  var $host;
//  var $user;
//  var $pass;
//  var $base;
//  var $port;
//  
//  var $_link;
//  var $_errors = array();
//  
//  function CDBConnection($host, $user, $pass, $base = "", $port = "3306") {
//    $this->host = $host;
//    $this->user = $user;
//    $this->pass = $pass;
//    $this->base = $base;
//    $this->port = $port;
//  }
//  
//  function logError($prefix) {
//    if (false == $this->_link) {
//      $prefix .= "\nErreur MySQL #" . mysql_errno($this->_link) . ": " . mysql_error($this->_link);
//    }
//    $this->_errors[] = $prefix;
//  }
//
//  function emptyLogs() {
//    $this->_errors = array();
//  }
//  
//  function connect() {
//    if ($errorMsg = $this->_connect()) {
//      $this->logError($errorMsg);
//      return false;
//    } 
//    
//    return true;
//  }
//    
//  function _connect() {
//    if (false == function_exists("mysql_connect")) {
//      return "Support MySQL non disponible. Merci de revenir à la première étape.";
//    }
//    
//    if (false == $this->_link = @mysql_connect("$this->host:$this->port", $this->user, $this->pass)) {
//      return "Impossible de se connecter au serveur de base de données sur l'hôte '$this->host' avec l'utilisateur '$this->user'.";
//    }
//    
//    if ($this->base) {
//      if (false == @mysql_select_db($this->base, $this->_link)) {
//        return "Impossible de se sélectionner la base de données '$this->base'.";
//      }
//    }
//  }
//  
//  function query($query) {
//    if (false == $result = mysql_query($query, $this->_link)) {
//      $this->_errors[] .= "\nErreur MySQL #" . mysql_errno($this->_link) . ": " . mysql_error($this->_link);
//    }
//  }
//  
//  function queryDump($path) {
//   $sqlLines = file($path);
//   $query = "";
//   foreach($sqlLines as $lineNumber => $sqlLine) {
//     $sqlLine = trim($sqlLine);
//     if (($sqlLine != "") && (substr($sqlLine, 0, 2) != "--") && (substr($sqlLine, 0, 1) != "#")) {
//       $query .= $sqlLine;
//       if (preg_match("/;\s*$/", $sqlLine)) {
//         $this->query($query);
//         $query = "";
//       }
//     }
//   }
//  }
//}
