<?php

/**
 * A13 - Cancel discharge/end visit - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2EventADTA13
 * A13 - Cancel discharge/end visit
 */
class CHL7v2EventADTA13 extends CHL7v2EventADT implements CHL7EventADTA01 {
  var $code        = "A13";
  var $struct_code = "A01";
  
  function __construct($i18n = null) {
    parent::__construct($i18n);
        
    $this->transaction = CPAM::getTransaction($this->code);
  }
  
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
  }
  
}

?>