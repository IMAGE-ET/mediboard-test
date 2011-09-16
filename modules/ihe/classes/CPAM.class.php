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
    // ITI-30
    "A28" => "CHL7EventADTA28",
    "A31" => "CHL7EventADTA31",
    "A40" => "CHL7EventADTA40",
    
    // ITI-31
    "A05" => "CHL7EventADTA05",
    "A38" => "CHL7EventADTA38",
    "Z99" => "CHL7EventADTZ99",
  );

  function getEvenements() {
    return self::$evenements;
  }
  
  function __construct() {
    $this->type = "PAM";

    
  }
}

?>