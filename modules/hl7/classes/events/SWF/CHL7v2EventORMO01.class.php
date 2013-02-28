<?php

/**
 * O01 - Order Message - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2EventORMO01
 * O01 - Order Message
 */
class CHL7v2EventORMO01 extends CHL7v2EventORM implements CHL7EventORMO01 {
  /**
   * @var string
   */
  public $code = "O01";

  /**
   * Build O01 event
   *
   * @param CMbObject $object Object
   *
   * @see parent::build()
   *
   * @return void
   */
  function build($object) {
    parent::build($object);


  }
}