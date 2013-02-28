<?php

/**
 * A44 - Move account information - patient account number - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2EventADTA44
 * A44 - Move account information - patient account number
 */
class CHL7v2EventADTA44 extends CHL7v2EventADT implements CHL7EventADTA43 {
  /**
   * @var string
   */
  public $code        = "A44";
  /**
   * @var string
   */
  public $struct_code = "A43";

  /**
   * Get event planned datetime
   *
   * @param CSejour $sejour Admit
   *
   * @return DateTime Event occured
   */
  function getEVNPlannedDateTime($sejour) {
    return mbDateTime();
  }

  /**
   * Build A44 event
   *
   * @param CSejour $sejour Admit
   *
   * @see parent::build()
   *
   * @return void
   */
  function build($sejour) {
    parent::build($sejour);

    $patient = $sejour->_ref_patient;
    // Patient Identification
    $this->addPID($patient, $sejour);

    // Patient Additional Demographic
    $this->addPD1($patient);

    $old_patient = new CPatient();
    $old_patient->load($sejour->_old->patient_id);
    // Merge Patient Information
    $this->addMRG($old_patient);
  }
}