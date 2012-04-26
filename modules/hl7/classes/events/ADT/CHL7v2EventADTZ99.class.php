<?php

/**
 * Z99 - Change admit - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2EventADTZ99
 * Z99 - Change admit - HL7
 */
class CHL7v2EventADTZ99 extends CHL7v2EventADT implements CHL7EventADTZ99 {
  var $code        = "Z99";
  var $struct_code = "Z99";
  
  function __construct($i18n = null) {
    parent::__construct($i18n);
  }
  
  function getEVNOccuredDateTime(CSejour $sejour) {
    return $sejour->entree_reelle;
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
    
    // Doctors
    $this->addROLs($patient);
    
    // Next of Kin / Associated Parties
    $this->addNK1s($patient);
    
    // Patient Visit
    $this->addPV1($sejour);
    
    // Patient Visit - Additionale Info
    $this->addPV2($sejour);
    
    // Build specific segments (i18n)
    $this->buildI18nSegments($sejour);
    
    // Guarantor
    $this->addGT1($patient);
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