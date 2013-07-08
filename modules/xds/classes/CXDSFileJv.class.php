<?php

/**
 * $Id$
 *  
 * @category XDS
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */
 
/**
 * Permet d'effectuer des actions sur les fichiers de jeux de valeurs
 */
class CXDSFileJv {

  public $handle;

  function __construct($file) {
    if (is_string($file)) {
      $this->handle = fopen($file, "r+");
    }
  }

  /**
   * Read a line of the file
   *
   * @return array An indexed array containing the fields read
   */
  function readLine() {
    $line = fgets($this->handle);

    if (!empty($line)) {
      $line = explode(";", $line);
    }
    return $line;
  }
}
