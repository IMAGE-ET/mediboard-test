<?php

/**
 * Z99 - Change admit - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

CAppUI::requireModuleClass("hl7", "CHL7v2EventADT");
CAppUI::requireModuleClass("hl7", "CHL7EventADTZ99");

/**
 * Class CHL7v2EventADTZ99
 * Z99 - Change admit
 */
class CHL7v2EventADTZ99 extends CHL7v2EventADT implements CHL7EventADTZ99 {
  function __construct() {
    parent::__construct();
        
    $this->code      = "Z99";
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