<?php

/**
 * A52 - Cancel Leave of Absence for a Patient - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2EventADTA52
 * A52 - Cancel Leave of Absence for a Patient
 */
class CHL7v2EventADTA52 extends CHL7v2EventADT implements CHL7EventADTA52 {
  /**
   * @var string
   */
  public $code        = "A52";
  /**
   * @var string
   */
  public $struct_code = "A52";

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
   * Build A52 event
   *
   * @param CAffectation $affectation Affectation
   *
   * @see parent::build()
   *
   * @return void
   */
  function build($affectation) {
    /** @var CSejour $sejour */
    $sejour                       = $affectation->_ref_sejour;
    $sejour->_ref_hl7_affectation = $affectation;
    
    parent::build($affectation);

    /** @var CPatient $patient */
    $patient = $sejour->_ref_patient;
    // Patient Identification
    $this->addPID($patient, $sejour);
    
    // Patient Additional Demographic
    $this->addPD1($patient);
    
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

    // Movement segment only used within the context of the "Historic Movement Management"
    if ($this->_receiver->_configs["iti31_historic_movement"]) {
      $this->addZBE($sejour);
    }
  }
}