<?php

/**
 * A45 - Change Patient ID - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2EventADTA45
 * A45 - Move visit information - visit number
 */
class CHL7v2EventADTA45 extends CHL7v2EventADT implements CHL7EventADTA45 {
  /** @var string */
  public $code        = "A45";

  /** @var string */
  public $struct_code = "A45";

  /**
   * Build A45 event
   *
   * @param CSejour $sejour Admit
   *
   * @see parent::build()
   *
   * @return void
   */
  function build($sejour) {
  }
}