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
 * Class CHL7v2EventADTA50
 * A50 - Change visit number
 */
class CHL7v2EventADTA50 extends CHL7v2EventADT implements CHL7EventADTA50 {

  /** @var string */
  public $code        = "A50";

  /** @var string */
  public $struct_code = "A50";

  /**
   * Build A50 event
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