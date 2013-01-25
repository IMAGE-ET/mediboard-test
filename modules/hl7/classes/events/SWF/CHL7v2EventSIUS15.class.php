<?php

/**
 * S15 - Notification of appointment cancellation - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2EventSIUS15
 * S15 - Notification of appointment cancellation
 */
class CHL7v2EventSIUS15 extends CHL7v2EventSIU implements CHL7EventSIUS12 {
  /**
   * @var string
   */
  var $code = "S15";

  /**
   * Build S15 event
   *
   * @param CConsultation $appointment Appointment
   *
   * @see parent::build()
   *
   * @return void
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