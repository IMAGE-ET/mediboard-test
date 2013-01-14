<?php

/**
 * A47 - Change patient identifier list - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2EventADTA47
 * A47 - Change patient identifier list
 */
class CHL7v2EventADTA47 extends CHL7v2EventADT implements CHL7EventADTA30 {
  /**
   * @var string
   */
  var $code        = "A47";
  /**
   * @var string
   */
  var $struct_code = "A30";

  /**
   * Build A47 event
   *
   * @param CPatient $patient Person
   *
   * @see parent::build()
   *
   * @return void
   */
  function build($patient) {
    parent::build($patient);
    
    // Patient Identification
    $this->addPID($patient);
    
    // Patient Additional Demographic
    $this->addPD1($patient);
    
    // Merge Patient Information
    $this->addMRG($patient->_patient_elimine);
  }
  
}

?>