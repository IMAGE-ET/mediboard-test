<?php

/**
 * R01 - Observation results reports for the patient - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2EventORUR01
 * R01 - Observation results reports for the patient
 */
class CHL7v2EventORUR01 extends CHL7v2EventDEC implements CHL7EventORUR01 {
  function __construct($i18n = null) {
    parent::__construct($i18n);
        
    $this->code        = "R01";
    $this->transaction = "PCD01";
    $this->msg_codes   = array ( 
      array(
        $this->event_type, $this->code
      )
    );
  }
  
  function build($sejour) {
    parent::build($sejour);
    
    
  }
  
}

?>