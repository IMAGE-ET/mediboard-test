<?php

/**
 * A08 - Update Patient Information - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2EventADTA08
 * A08 - Update Patient Information
 */
class CHL7v2EventADTA08 extends CHL7v2EventADT implements CHL7EventADTA01 {
  var $code        = "A08";
  var $struct_code = "A01";
  
  function __construct($i18n = null) {
    parent::__construct($i18n);
  }
  
  function getEVNOccuredDateTime($object) {
    return mbDateTime();
  }
  
  /**
   * @see parent::build()
   */
  function build($object) {
    if ($object instanceof CAffectation) {
      $sejour                       = $object->_ref_sejour;
      $sejour->_ref_hl7_affectation = $object;
    }
    else {
      $sejour = $object;
    }
    
    parent::build($sejour);
    
    $patient = $sejour->_ref_patient;
    // Patient Identification
    $this->addPID($patient, $sejour);
    
    // Patient Additional Demographic
    $this->addPD1($patient);
    
    // Next of Kin / Associated Parties
    $this->addNK1s($patient);
    
    // Patient Visit
    $this->addPV1($sejour);
    
    // Patient Visit - Additionale Info
    $this->addPV2($sejour);
  }
  
}

?>