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
class CPAM {
  static $evenements = array(
    "A28" => "CHL7MessageADTA28",
    "A31" => "CHL7MessageADTA31",
    "A40" => "CHL7MessageADTA40",
  );

  function getEvenements() {
    return self::$evenements;
  }
  
  function __construct() {
    $this->type = "PAM";

    
  }
}

?>