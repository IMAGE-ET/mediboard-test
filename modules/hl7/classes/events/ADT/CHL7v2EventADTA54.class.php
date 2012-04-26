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

/**
 * Class CHL7v2EventADTA54
 * A54 - Change attending doctor
 */
class CHL7v2EventADTA54 extends CHL7v2EventADT implements CHL7EventADTA54 {
  var $code        = "A54";
  var $struct_code = "A54";
  
  function __construct($i18n = null) {
    parent::__construct($i18n);
  }
  
  function getEVNOccuredDateTime($sejour) {
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
    
    // Patient Visit
    $this->addPV1($sejour);
    
    // Patient Visit - Additionale Info
    $this->addPV2($sejour);
    
    // Build specific segments (i18n)
    $this->buildI18nSegments($sejour);
  }
  
  /**
   * @see parent::buildI18nSegments()
   */
  function buildI18nSegments($sejour) {
    
    // Movement segment only used within the context of the "Historic Movement Management"
    if ($this->_receiver->_configs["iti31_historic_movement"]) {
      $this->addZBE($sejour);
    }
  }
}

?>