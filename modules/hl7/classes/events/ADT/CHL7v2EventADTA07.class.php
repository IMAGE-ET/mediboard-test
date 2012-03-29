<?php

/**
 * A07 - Change an inpatient to an outpatient - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2EventADTA07
 * A07 - Change an inpatient to an outpatient
 */
class CHL7v2EventADTA07 extends CHL7v2EventADT implements CHL7EventADTA06 {
  var $code        = "A07";
  var $struct_code = "A06";
  
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
    
    // Doctors
    $this->addROLs($patient);
    
    // Next of Kin / Associated Parties
    $this->addNK1s($patient);
    
    // Patient Visit
    $this->addPV1($sejour);
    
    // Build specific segments (i18n)
    $this->buildI18nSegments($sejour);
  }
  
}

?>