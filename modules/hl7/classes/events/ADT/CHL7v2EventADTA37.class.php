<?php

/**
 * A37 - Unlink patient information - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2EventADTA37
 * A37 - Unlink patient information
 */
class CHL7v2EventADTA37 extends CHL7v2EventADT implements CHL7EventADTA37 {
  var $code        = "A37";
  var $struct_code = "A37";
  
  function __construct($i18n = null) {
    parent::__construct($i18n);
  }
  
  function getEVNOccuredDateTime($sejour) {
    return mbDateTime();
  }
  
  /**
   * @see parent::build()
   */
  function build($patient) {
    parent::build($patient);
    
    // Patient Identification
    $this->addPID($patient);
    
    // Patient Additional Demographic
    $this->addPD1($patient);
    
    // Patient Visit
    $this->addPV1();
    
    /* @toto old ? */
    $patient_link = $patient->load($patient->_old->patient_link_id);
    // Patient link Identification
    $this->addPID($patient_link);
    
    // Patient link Additional Demographic
    $this->addPD1($patient_link);
    
    // Patient link Visit
    $this->addPV1();
  }
}

?>