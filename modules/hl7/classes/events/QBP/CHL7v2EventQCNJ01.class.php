<?php

  /**
   * Q22 - Find Candidates - HL7
   *
   * @category HL7
   * @package  Mediboard
   * @author   SARL OpenXtrem <dev@openxtrem.com>
   * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
   * @version  SVN: $Id:$
   * @link     http://www.mediboard.org
   */

  /**
   * Class CHL7v2EventPDQQ22
   * J01 - PDQ Cancel Query
   */
class CHL7v2EventQCNJ01 extends CHL7v2EventQCN implements CHL7EventQCNJ01 {
  /**
   * @var string
   */
  public $code        = "J01";

  /**
   * @var string
   */
  public $struct_code = "J01";

  /**
   * Build J01 event
   *
   * @param CPatient $patient Person
   *
   * @see parent::build()
   *
   * @return void
   */
  function build($patient) {
    parent::build($patient);


  }
}