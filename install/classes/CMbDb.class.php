<?php
/**
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage Installer
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    SVN: $Id$ 
 * @link       http://www.mediboard.org
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
  public $dsn = array();

  /**
   * @var DB_common
   */
  public $_db;

  /**
   * @var string[]
   */
  public $_errors = array();

  /**
   * Constructor
   *
   * @param string      $host SQL server hostname
   * @param string      $user Username
   * @param string      $pass Password
   * @param bool|string $base Database name
   *
   * @return CMbDb
   */
  function __construct($host, $user, $pass, $base = false) {
    $parts = explode(":", $host);
    $this->dsn = array(
      "phptype"  => "mysql",
      "username" => $user,
      "password" => $pass,
      "hostspec" => $parts[0],
      "port"     => isset($parts[1]) ? $parts[1] : 3306,
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
  function logError(PEAR_Error $error, $prefix = "") {
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
    if ($this->_db instanceof PEAR_Error) {
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
   * @return mixed The actual result if successfull, false otherwise
   */
  function query($query, $data = array()) {
    $res =& $this->_db->query($query, $data);
    if ($res instanceof PEAR_Error) {
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
   * @return mixed The actual result if successful, false otherwise
   */
  function getOne($query, $data = array()) {
    $res =& $this->_db->getOne($query, $data);
    if ($res instanceof PEAR_Error) {
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
   * @return mixed The actual result if successful, false otherwise
   */
  function getAssoc($query, $data = array()) {
    $res =& $this->_db->getAssoc($query, true, $data, DB_FETCHMODE_ASSOC);
    if ($res instanceof PEAR_Error) {
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
      if (!$_line) {
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
