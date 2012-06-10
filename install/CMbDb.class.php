<?php
/**
 * Generate system user SQL queries
 * 
 * @package    Mediboard
 * @subpackage install
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage install
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

require_once "DB.php";

/**
 * PEAR::DB encapulation for install scripts
 * Responsibilities:
 *  - datasource connection
 *  - querying
 *  - error reporting
 */
class CMbDb {
  var $dsn = array();
  
  var $_db;
  var $_errors = array();
  
  /**
   * Constructor
   * 
   * @param string $host SQL server hostname
   * @param string $user Username
   * @param string $pass Password
   * @param string $base Database name
   * @param string $port Used port
   * 
   * @return void
   */
  function __construct($host, $user, $pass, $base = false, $port = "3306") {
    $this->dsn = array(
      "phptype"  => "mysql",
      "username" => $user,
      "password" => $pass,
      "hostspec" => $host,
      "port"     => $port,
      "database" => $base,
    );
  }
  
  /**
   * Log error in a array with appropriate message
   * 
   * @param PEAR_Error $error  The PEAR error
   * @param string     $prefix Give a prefix to error Message 
   * 
   * @return void
   */
  function logError($error, $prefix = "") {
    $prefix .= sprintf("\nErreur # %s\nMessage: %s, \nInfo: %s", $error->code, $error->message, $error->userinfo);
    $this->_errors[] = $prefix;
  }

  /**
   * Empty errors array
   * 
   * @return void
   */
  function emptyErrors() {
    $this->_errors = array();
  }
  
  /**
   * Try ton connect to database with dsn properties
   * 
   * @return bool Job-done value
   */
  function connect() {
    $this->_db =& DB::connect($this->dsn);
    if (PEAR::isError($this->_db)) {
      $this->logError($this->_db, "Problème de connexion");
      return false;
    } 
    
    return true;
  }
  
  /**
   * Query the data source
   * 
   * @param string $query SQL query
   * @param array  $data  Preparation data
   * 
   * @return result The actual result if successfull, false otherwise
   */
  function query($query, $data = array()) {
    $res =& $this->_db->query($query, $data);
    if (PEAR::isError($res)) {
      $this->logError($res, "Erreur d'exécution de requête");
      return false;
    }

    return $res;
  }
  
  /**
   * Execute a get one query on data source
   * 
   * @param string $query SQL query
   * @param array  $data  Preparation data
   * 
   * @return result The actual result if successfull, false otherwise
   */
  function getOne($query, $data = array()) {
    $res =& $this->_db->getOne($query, $params);
    if (PEAR::isError($res)) {
      $this->logError($res, "Erreur d'exécution de requête");
      return false;
    }

    return $res;
  }
  
  /**
   * Query a whole dump on data source
   * 
   * @param string $path File path of the dump
   * 
   * @return void
   */
  function queryDump($path) {
    $lines = file($path);
    $query = "";
    foreach ($lines as $_line) {
      $_line = trim($_line);
      
      // Ignore empty lines
      if (!$line) {
        continue;
      }
      
      // Ignore comments
      if (substr($_line, 0, 2) == "--" || substr($_line, 0, 1) == "#" ) {
        continue;
      }
      
      // Append line to query
      $query .= $_line;
      
      // Execute only if query is terminated by a semicolumn
      if (preg_match("/;\s*$/", $_line)) {
        $this->query($query);
        $query = "";
      }
    }
  }
}
