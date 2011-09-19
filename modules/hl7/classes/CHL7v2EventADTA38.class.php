<?php

/**
 * A38 - Cancel pre-admit  - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

CAppUI::requireModuleClass("hl7", "CHL7v2EventADT");
CAppUI::requireModuleClass("hl7", "CHL7EventADTA38");

/**
 * Class CHL7v2EventADTA38
 * A38 - Cancel pre-admit 
 */
class CHL7v2EventADTA38 extends CHL7v2EventADT implements CHL7EventADTA38 {
  function __construct() {
    parent::__construct();
        
    $this->code      = "A38";
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