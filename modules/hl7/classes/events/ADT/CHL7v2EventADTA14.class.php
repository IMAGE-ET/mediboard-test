<?php

/**
 * A14 - Pending Admit - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2EventADTA14
 * A14 - Pending Admit
 */
class CHL7v2EventADTA14 extends CHL7v2EventADT implements CHL7EventADTA14 {

  /** @var string */
  public $code        = "A14";

  /** @var string */
  public $struct_code = "A05";

  /**
   * Get event planned datetime
   *
   * @param CSejour $sejour Admit
   *
   * @return DateTime Event planned
   */
  function getEVNPlannedDateTime(CSejour $sejour) {
    return $sejour->entree_reelle;
  }

  /**
   * Build A14 event
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