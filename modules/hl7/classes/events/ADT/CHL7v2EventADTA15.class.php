<?php

/**
 * A15 - Pending Transfer - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2EventADTA15
 * A15 - Pending Transfer
 */
class CHL7v2EventADTA15 extends CHL7v2EventADT implements CHL7EventADTA15 {
  /**
   * @var string
   */
  public $code        = "A15";
  /**
   * @var string
   */
  public $struct_code = "A15";

  /**
   * Get event planned datetime
   *
   * @param CSejour $sejour Admit
   *
   * @return DateTime Event planned
   */
  function getEVNPlannedDateTime($sejour) {
    return null;
  }

  /**
   * Build A15 event
   *
   * @param CSejour $sejour Admit
   *
   * @see parent::build()
   *
   * @return void
   */
  function build($sejour) {
    parent::build($sejour);

    /** @var CPatient $patient */
    $patient = $sejour->_ref_patient;
    // Patient Identification
    $this->addPID($patient, $sejour);
    
    // Patient Additional Demographic
    $this->addPD1($patient);
    
    // Doctors
    $this->addROLs($patient);

    // Patient Visit
    $this->addPV1($sejour);
    
    // Patient Visit - Additionale Info
    $this->addPV2($sejour);
    
    // Build specific segments (i18n)
    $this->buildI18nSegments($sejour);
  }

  /**
   * Build i18n segements
   *
   * @param CSejour $sejour Admit
   *
   * @see parent::buildI18nSegments()
   *
   * @return void
   */
  function buildI18nSegments($sejour) {
  }
}