<?php

/**
 * K23 - Query Response - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2EventQBPK23
 * K23 - Query Response
 */
class CHL7v2EventQBPK23 extends CHL7v2EventQBP implements CHL7EventQBPK23 {

  /** @var string */
  public $code = "K23";

  /**
   * Construct
   *
   * @return \CHL7v2EventQBPK23
   */
  function __construct() {
    parent::__construct();

    $this->profil = "PIX";
  }

  /**
   * Build K22 event
   *
   * @param CPatient $patient Person
   *
   * @see parent::build()
   *
   * @return void
   */
  function build($patient) {
  }
}