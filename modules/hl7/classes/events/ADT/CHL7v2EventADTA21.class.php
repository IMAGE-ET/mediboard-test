<?php

/**
 * A21 - Patient goes on a _leave of absence_ - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2EventADTA21
 * A21 - Patient goes on a _leave of absence_
 */
class CHL7v2EventADTA21 extends CHL7v2EventADT implements CHL7EventADTA21 {
  /**
   * @var string
   */
  public $code        = "A21";
  /**
   * @var string
   */
  public $struct_code = "A21";

  /**
   * Get event planned datetime
   *
   * @param CAffectation $affectation Affectation
   *
   * @return DateTime Event occured
   */
  function getEVNOccuredDateTime($affectation) {
    return $affectation->entree;
  }

  /**
   * Build A21 event
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
    
    parent::build($sejour);

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
}