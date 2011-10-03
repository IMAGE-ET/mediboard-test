<?php

/**
 * A54 - Change attending doctor - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

CAppUI::requireModuleClass("hl7", "CHL7v2EventADT");
CAppUI::requireModuleClass("hl7", "CHL7EventADTA54");

/**
 * Class CHL7v2EventADTA54
 * A54 - Change attending doctor
 */
class CHL7v2EventADTA54 extends CHL7v2EventADT implements CHL7EventADTA54 {
  function __construct() {
    parent::__construct();
        
    $this->code      = "A54";
    $this->msg_codes = array ( 
      array(
        $this->event_type, $this->code
      )
    );
  }
  
  function build($sejour) {
    parent::build($sejour);
    
    $patient = $sejour->_ref_patient;
    // Patient Identification
    $this->addPID($patient);
    
    // Patient Additional Demographic
    $this->addPD1($patient);
    
    // Patient Visit
    $this->addPV1($sejour);
    
    // Patient Visit - Additionale Info
    $this->addPV2($sejour);
    
    // Movement segment
    $this->addZBE($sejour);
  }
  
}

?>