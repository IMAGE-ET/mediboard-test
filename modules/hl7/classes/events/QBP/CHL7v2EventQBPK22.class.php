<?php

/**
 * K22 - Find Candidates response - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2EventPDQK22
 * K22 - Find Candidates response
 */
class CHL7v2EventQBPK22 extends CHL7v2EventQBP implements CHL7EventQBPK22 {
  var $code = "K22";
  
  function __construct($i18n = null) {
    parent::__construct($i18n);
  }
  
  /**
   * @see parent::build()
   */
  function build($patient) {
  }
}

?>