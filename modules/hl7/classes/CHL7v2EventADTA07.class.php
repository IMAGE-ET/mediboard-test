<?php

/**
 * A07 - Change an inpatient to an outpatient - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2EventADTA07
 * A07 - Change an inpatient to an outpatient
 */
class CHL7v2EventADTA07 extends CHL7v2EventADT implements CHL7EventADTA06 {
  function __construct() {
    parent::__construct();
        
    $this->code      = "A07";
    $this->msg_codes = array ( 
      array(
        $this->event_type, $this->code, "{$this->event_type}_A06"
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
    
    // Doctors
    $this->addROLs($patient);
    
    // Next of Kin / Associated Parties
    $this->addNK1s($patient);
    
    // Patient Visit
    $this->addPV1($sejour);
    
    // Patient Visit - Additionale Info
    $this->addPV2($sejour);
    
    // Movement segment
    $this->addZBE($sejour);
  }
  
}

?>