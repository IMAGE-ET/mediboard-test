<?php

/**
 * A08 - Update Patient Information - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2EventADTA08
 * A08 - Update Patient Information
 */
class CHL7v2EventADTA08 extends CHL7v2EventADT implements CHL7EventADTA01 {
  /**
   * @var string
   */
  public $code        = "A08";
  /**
   * @var string
   */
  public $struct_code = "A01";

  /**
   * Get event planned datetime
   *
   * @param CMbObject $object Object
   *
   * @return DateTime Event occured
   */
  function getEVNOccuredDateTime($object) {
    return ($object instanceof CAffectation) ? $object->entree : mbDateTime();
  }

  /**
   * Build A08 event
   *
   * @param CMbObject $object Object
   *
   * @see parent::build()
   *
   * @return void
   */
  function build($object) {
    if ($object instanceof CAffectation) {
      $affectation= $object;

      /** @var CSejour $sejour */
      $sejour                       = $affectation->_ref_sejour;
      $sejour->_ref_hl7_affectation = $affectation;

      parent::build($affectation);
    }
    else {
      $sejour = $object;

      parent::build($sejour);
    }

    /** @var CPatient $patient */
    $patient = $sejour->_ref_patient;
    // Patient Identification
    $this->addPID($patient, $sejour);
    
    // Patient Additional Demographic
    $this->addPD1($patient);
    
    // Next of Kin / Associated Parties
    $this->addNK1s($patient);
    
    // Patient Visit
    $this->addPV1($sejour);
    
    // Patient Visit - Additionale Info
    $this->addPV2($sejour);
  }
  
}