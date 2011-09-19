<?php

/**
 * A05 - Pre-admit a patient - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

CAppUI::requireModuleClass("hl7", "CHL7v2EventADT");
CAppUI::requireModuleClass("hl7", "CHL7EventADTA05");

/**
 * Class CHL7v2EventADTA05
 * A05 - Pre-admit a patient
 */
class CHL7v2EventADTA05 extends CHL7v2EventADT implements CHL7EventADTA05 {
  function __construct() {
    parent::__construct();
        
    $this->code      = "A05";
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