<?php

/**
 * Patient Administration Management IHE
 *  
 * @category IHE
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CPAM 
 * Patient Administration Management
 */
class CPAM extends CMbObject {
  static $evenements = array(
    'ITI30' => "CITI30",
    'ITI31' => "CITI31"
  );

  function getEvenements() {
    return self::$evenements;
  }
  
  function __construct() {
    $this->evenement = "evenementsPatient";
    $this->type      = "PAM";
                
  }
}

?>