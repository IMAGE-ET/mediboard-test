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

/**
 * PDO encapulation for install scripts
 * Responsibilities:
 *  - datasource connection
 *  - querying
 *  - error reporting
 */
class CMbDb extends PDO {
  /**
   * Constructor
   *
   * @param string      $host SQL server hostname
   * @param string      $user Username
   * @param string      $pass Password
   * @param null|string $base Database name
   *
   * @return self
   */
  function __construct($host, $user, $pass, $base = null) {
    $dsn = "mysql:host=$host";

    if ($base) {
      $dsn .= ";dbname=$base";
    }

    parent::__construct($dsn, $user, $pass);
  }

  /**
   * Get STD datasource
   *
   * @return self
   */
  static function getStd() {
    global $dPconfig;

    $dbConfigs = $dPconfig["db"];
    $dbConfig = $dbConfigs["std"];

    return new CMbDb(
      $dbConfig["dbhost"],
      $dbConfig["dbuser"],
      $dbConfig["dbpass"],
      $dbConfig["dbname"]
    );
  }
  
  /**
   * Query the data source
   * 
   * @param string $query SQL query
   * @param array  $data  Data
   * 
   * @return mixed The actual result if successfull, false otherwise
   */
  function query($query, $data = array()) {
    $stmt = $this->prepare($query);
    return $stmt->execute($data);
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
    $row = $this->getAssoc($query, $data);

    if ($row === false) {
      return false;
    }

    return reset($row);
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
    $stmt = $this->prepare($query);
    $stmt->execute($data);

    return $stmt->fetch(PDO::FETCH_ASSOC);
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
