<?php

/**
 * S14 - Notification of appointment modification - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2EventSIUS14
 * S14 - Notification of appointment modification
 */
class CHL7v2EventSIUS14 extends CHL7v2EventSWF implements CHL7EventSIUS12 {
  var $code = "S14";
  
  function __construct($i18n = null) {
    parent::__construct($i18n);
  }
  
  /**
   * @see parent::build()
   */
  function build($sejour) {
    parent::build($sejour);
    
    
  }
}

?>