<?php

/**
 * S12 - Notification of new appointment booking - HL7
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
 * S12 - Notification of new appointment booking 
 */
class CHL7v2EventSIUS12 extends CHL7v2EventSWF implements CHL7EventSIUS12 {
  var $code = "S12";
  
  function __construct($i18n = null) {
    parent::__construct($i18n);
  }
  
  /**
   * @see parent::build()
   */
  function build($scheduling) {
    parent::build($scheduling);
    
    // Scheduling Activity Information
    $this->addSCH($scheduling);
    
    $patient = $scheduling->loadRefPatient();
    // Patient Identification
    $this->addPID($patient);
    
    // Patient Additional Demographic
    $this->addPD1($patient);
    
    // Patient Visit
    $this->addPV1($scheduling);
    
    // Resource Group
    $this->addRGS($scheduling);
    
    // Appointment Information - General Resource
    $this->addAIG($scheduling);
    
    // Appointment Information - Location Resource
    $this->addAIL($scheduling);
  }
}

?>