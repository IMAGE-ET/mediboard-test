<?php

/**
 * Q23 -  Get corresponding identifiers  - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2EventQBPQ23
 * Q23 - Get corresponding identifiers
 */
class CHL7v2EventQBPQ23 extends CHL7v2EventQBP implements CHL7EventQBPQ23 {

  /** @var string */
  public $code        = "Q23";


  /** @var string */
  public $struct_code = "Q21";

  /**
   * Construct
   *
   * @return \CHL7v2EventQBPQ23
   */
  function __construct() {
    parent::__construct();

    $this->profil = "PIX";
  }

  /**
   * Build Q23 event
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
  }
}