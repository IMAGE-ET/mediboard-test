<?php

/**
 * S13 - Notification of appointment rescheduling - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2EventSIUS12
 * S13 - Notification of appointment rescheduling
 */
class CHL7v2EventSIUS13 extends CHL7v2EventSWF implements CHL7EventSIUS12 {
  var $code = "S13";
  
  function __construct($i18n = null) {
    parent::__construct($i18n);
  }
  
  /**
   * @see parent::build()
   */
  function build($appointment) {
    parent::build($appointment);
    
    // Scheduling Activity Information
    $this->addSCH($appointment);
    
    $patient = $appointment->loadRefPatient();
    // Patient Identification
    $this->addPID($patient);
    
    // Patient Additional Demographic
    $this->addPD1($patient);
    
    // Patient Visit
    $this->addPV1($appointment);
    
    // Resource Group
    $this->addRGS($appointment);
    
    // Appointment Information - General Resource
    $this->addAIG($appointment);
    
    // Appointment Information - Location Resource
    $this->addAIL($appointment);
  }
}

?>