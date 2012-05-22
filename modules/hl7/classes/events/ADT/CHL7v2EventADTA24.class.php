<?php

/**
 * A24 - Link patient information - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2EventADTA24
 * A24 - Link patient information
 */
class CHL7v2EventADTA24 extends CHL7v2EventADT implements CHL7EventADTA24 {
  var $code        = "A24";
  var $struct_code = "A24";
  
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
    
    $patient_links = $patient->loadPatientLinks();
    /* @toto first ? */
    
    $patient_link = $patient_links[0];
    // Patient link Identification
    $this->addPID($patient_link);
    
    // Patient link Additional Demographic
    $this->addPD1($patient_link);
    
    // Patient link Visit
    $this->addPV1();
  }
}

?>