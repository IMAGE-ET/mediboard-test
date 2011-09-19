<?php

/**
 * A11 - Cancel admit/visit notification - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

CAppUI::requireModuleClass("hl7", "CHL7v2EventADT");
CAppUI::requireModuleClass("hl7", "CHL7EventADTA11");

/**
 * Class CHL7v2EventADTA11
 * A11 - Cancel admit/visit notification
 */
class CHL7v2EventADTA11 extends CHL7v2EventADT implements CHL7EventADTA11 {
  function __construct() {
    parent::__construct();
        
    $this->code      = "A11";
    $this->msg_codes = array (
      array(
        $this->event_type, $this->code, "{$this->event_type}_A09"
      )
    );
  }
  
  function build($patient) {
    parent::build($patient);
    
    
  }
  
}

?>