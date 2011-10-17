<?php

/**
 * A55 - Cancel change attending doctor - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2EventADTA55
 * A55 - Cancel change attending doctor
 */
class CHL7v2EventADTA55 extends CHL7v2EventADT implements CHL7EventADTA52 {
  function __construct() {
    parent::__construct();
        
    $this->code        = "A55";
    $this->transaction = CPAM::getTransaction($this->code);
    $this->msg_codes   = array ( 
      array(
        $this->event_type, $this->code, "{$this->event_type}_A52"
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