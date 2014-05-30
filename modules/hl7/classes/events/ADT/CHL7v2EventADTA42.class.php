<?php

/**
 * A46 - Change Patient ID - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2EventADTA42
 * A46 - Change Patient ID
 */
class CHL7v2EventADTA42 extends CHL7v2EventADT implements CHL7EventADTA30 {

  /** @var string */
  public $code        = "A42";

  /** @var string */
  public $struct_code = "A39";

  /**
   * Build A42 event
   *
   * @param CSejour $sejour Sejour
   *
   * @see parent::build()
   *
   * @return void
   */
  function build($sejour) {
    parent::build($sejour);
    $patient = $sejour->_ref_patient;

    // Patient Identification
    $this->addPID($patient);
    
    // Patient Additional Demographic
    $this->addPD1($patient);
    
    // Merge Patient Information
    $this->addMRG($sejour->_sejour_elimine);

    $this->addPV1($sejour);
  }
}