<?php

/**
 * A01 - Admit/visit notification - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

CAppUI::requireModuleClass("hl7", "CHL7v2EventADT");
CAppUI::requireModuleClass("hl7", "CHL7EventADTA01");

/**
 * Class CHL7v2EventADTA01
 * A01 - Admit/visit notification
 */
class CHL7v2EventADTA01 extends CHL7v2EventADT implements CHL7EventADTA01 {
  function __construct() {
    parent::__construct();
        
    $this->code      = "A01";
    $this->msg_codes = array ( 
      array(
        $this->event_type, $this->code
      )
    );
  }
  
  function build($patient) {
    parent::build($patient);
    
    
  }
  
}

?>