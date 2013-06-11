<?php

/**
 * ZV1 - Find Admit Candidates - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2EventQBPZV1
 * ZV1 - Find Admit Candidates
 */
class CHL7v2EventQBPZV1 extends CHL7v2EventQBP implements CHL7EventQBPZV1 {

  /** @var string */
  public $code        = "ZV1";


  /** @var string */
  public $struct_code = "Q21";

  /**
   * Build ZV1 event
   *
   * @param CPatient $patient Person
   *
   * @see parent::build()
   *
   * @return void
   */
  function build($patient) {
    parent::build($patient);

    // QPD
    $this->addQPD($patient);

    // RCP
    $this->addRCP($patient);

    // DSC
    if (isset($patient->_pointer)) {
      $this->addDSC($patient);
    }
  }
}