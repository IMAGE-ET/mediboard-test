<?php

/**
 * $Id$
 *  
 * @category Classes
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */
 
/**
 * Class CPDOODBCDataSource
 */
class CPDOODBCDataSource extends CPDODataSource {

  protected $driver_name = "odbc";

  /**
   * Get the used grammar
   *
   * @return CSQLGrammarSQLServer|mixed
   */
  function getQueryGrammar() {
    return new CSQLGrammarSQLServer();
  }

  /**
   * Connection
   *
   * @param string $host
   * @param string $name
   * @param string $user
   * @param string $pass
   *
   * @return PDO|resource
   */
  function connect($host, $name, $user, $pass) {
    if (!class_exists("PDO")) {
      trigger_error("FATAL ERROR: PDO support not available. Please check your configuration.", E_USER_ERROR);
      return;
    }

    if (!$name) {
      $dsn = "$this->driver_name:$host";
    }
    else {
      $dsn = "$this->driver_name:$host;Database=$name;";
    }

    $link = new PDO($dsn, $user, $pass);
    $link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

    return $this->link = $link;
  }
}
