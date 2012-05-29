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

/**
 * Class CHL7v2EventADTA44
 * A44 - Move account information - patient account number
 */
class CHL7v2EventADTA44 extends CHL7v2EventADT implements CHL7EventADTA43 {
  var $code        = "A44";
  var $struct_code = "A43";
  
  function __construct($i18n = null) {
    parent::__construct($i18n);
  }
  
  function getEVNPlannedDateTime($sejour) {
    return mbDateTime();
  }
  
  /**
   * @see parent::build()
   */
  function build($sejour) {
    parent::build($sejour);
    
    $patient = $sejour->_ref_patient;
    // Patient Identification
    $this->addPID($patient, $sejour);
    
    // Patient Additional Demographic
    $this->addPD1($patient);
    
    $old_patient = new CPatient();
    $old_patient->load($sejour->_old->patient_id);
    // Merge Patient Information
    $this->addMRG($old_patient);
  }
  
}

?>