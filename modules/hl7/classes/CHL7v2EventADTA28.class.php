<?php

/**
 * A28 - Add person information - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

CAppUI::requireModuleClass("hl7", "CHL7v2Event");
CAppUI::requireModuleClass("hl7", "CHL7EventADTA28");

/**
 * Class CHL7v2EventADTA28 
 * A28 - Add person information
 */
class CHL7v2EventADTA28 extends CHL7v2Event implements CHL7EventADTA28 {
  function __construct() {
    parent::__construct();
        
    $this->code      = "A28";
    $this->msg_codes = array(
      "ADT", "A28", "ADT", "A05"
    );
  }
  
  function build($patient) {
    parent::build($patient);

    $message = $this->message;
    
    // Message Header 
    $MSH = CHL7v2Segment::create("MSH", $message);
    $MSH->build($this);
    
    // Event Type
    $EVN = CHL7v2Segment::create("EVN", $message);
    $EVN->planned_datetime = null;
    $EVN->occured_datetime = null;
    $EVN->build($this);
    
    // Patient Identification
    $PID = CHL7v2Segment::create("PID", $message);
    $PID->patient = $patient;
    $PID->set_id  = 1;
    $PID->build($this);
    
    // Patient Additional Demographic
    $PD1 = CHL7v2Segment::create("PD1", $message);
    $PD1->patient = $patient;
    $PD1->build($this);
    
    // Patient Visit
    $PV1 = CHL7v2Segment::create("PV1", $message);
    $PV1->build($this);
  }
  
}

?>