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
  /**
   * @var string
   */
  public $code        = "A37";
  /**
   * @var string
   */
  public $struct_code = "A37";

  /**
   * Get event planned datetime
   *
   * @param CSejour $sejour Admit
   *
   * @return DateTime Event occured
   */
  function getEVNOccuredDateTime($sejour) {
    return CMbDT::dateTime();
  }

  /**
   * Build A37 event
   *
   * @param CPatient $patient Person
   *
   * @see parent::build()
   *
   * @return void
   */
  function build($patient) {
    parent::build($patient);
    
    // Patient Identification
    $this->addPID($patient);
    
    /* @toto old ? */
    $patient_link = new CPatient();
    $patient_link->load($patient->_old->patient_link_id);
    
    // Patient link Identification
    $this->addPID($patient_link);
  }
}