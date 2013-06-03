<?php

/**
 * Patient Registry Get Demographics Query
 * A user initiates a query to a patient registry requesting demographic information for a specific patient
 *
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v3EventPRPAIN201307UV02
 * Patient Registry Get Demographics Query
 */
class CHL7v3EventPRPAIN201307UV02 extends CHL7v3EventPRPA implements CHL7EventPRPAST201317UV02 {
  /**
   * @var string
   */
  public $code = "IN201307UV02";

  /**
   * Build IN201307UV02 event
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