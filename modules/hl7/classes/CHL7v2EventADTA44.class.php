<?php

/**
 * A44 - Move account information - patient account number - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

CAppUI::requireModuleClass("hl7", "CHL7v2EventADT");
CAppUI::requireModuleClass("hl7", "CHL7EventADTA43");

/**
 * Class CHL7v2EventADTA44
 * A44 - Move account information - patient account number
 */
class CHL7v2EventADTA44 extends CHL7v2EventADT implements CHL7EventADTA43 {
  function __construct() {
    parent::__construct();
        
    $this->code      = "A44";
    $this->msg_codes = array ( 
      array(
        $this->event_type, $this->code, "{$this->event_type}_A43"
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
    
    // Merge Patient Information
    $this->addMRG($patient->_patient_elimine);
  }
  
}

?>