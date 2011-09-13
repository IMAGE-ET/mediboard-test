<?php

/**
 * A40 - Merge patient - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

CAppUI::requireModuleClass("hl7", "CHL7v2Event");
CAppUI::requireModuleClass("hl7", "CHL7EventADTA40");

/**
 * Class CHL7v2EventADTA40
 * A40 - Merge patient
 */
class CHL7v2EventADTA40 extends CHL7v2EventADT implements CHL7EventADTA31 {
  function __construct() {
    parent::__construct();
        
    $this->code      = "A40";
    $this->msg_codes = array ( 
      array(
        $this->event_type, $this->code, "{$this->event_type}_A39"
      )
    );
  }
  
  function build($patient){
    parent::build($patient);
    
    
  }
}


?>