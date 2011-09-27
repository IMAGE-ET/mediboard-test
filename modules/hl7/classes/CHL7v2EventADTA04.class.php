<?php

/**
 * A04 - Register a patient - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

CAppUI::requireModuleClass("hl7", "CHL7v2EventADT");
CAppUI::requireModuleClass("hl7", "CHL7EventADTA04");

/**
 * Class CHL7v2EventADTA04
 * A04 - Register a patient
 */
class CHL7v2EventADTA04 extends CHL7v2EventADT implements CHL7EventADTA04 {
  function __construct() {
    parent::__construct();
        
    $this->code      = "A04";
    $this->msg_codes = array ( 
      array(
        $this->event_type, $this->code, "{$this->event_type}_A01"
      )
    );
  }
  
  function build($sejour) {
    parent::build($sejour);
    
    
  }
  
}

?>