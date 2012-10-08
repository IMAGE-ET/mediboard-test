<?php

/**
 * S15 - Notification of appointment cancellation - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2EventSIUS15
 * S15 - Notification of appointment cancellation
 */
class CHL7v2EventSIUS15 extends CHL7v2EventSWF implements CHL7EventSIUS12 {
  var $code = "S15";
  
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