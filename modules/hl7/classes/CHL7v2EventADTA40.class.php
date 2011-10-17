<?php

/**
 * A40 - Merge patient - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2EventADTA40
 * A40 - Merge patient
 */
class CHL7v2EventADTA40 extends CHL7v2EventADT implements CHL7EventADTA39 {
  function __construct() {
    parent::__construct();
        
    $this->code        = "A40";
    $this->transaction = CPAM::getTransaction($this->code);
    $this->msg_codes   = array ( 
      array(
        $this->event_type, $this->code, "{$this->event_type}_A39"
      )
    );
  }
  
  function build($patient){
    parent::build($patient);
    
    // Patient Identification
    $this->addPID($patient);
    
    // Patient Additional Demographic
    $this->addPD1($patient);
    
    // Merge Patient Information
    $this->addMRG($patient->_patient_elimine);
    
    // Patient Visit
    $this->addPV1();
  }
}


?>