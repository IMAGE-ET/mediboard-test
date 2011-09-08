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

/**
 * Class CHL7v2MessageADTA28 
 * A28 - Add person information
 */
class CHL7v2MessageADTA28 extends CHL7v2Event {
  function __construct() {
    parent::__construct();
        
    $this->code      = "A28";
    $this->msg_codes = array(
      "ADT", "A28", "ADT", "A05"
    );
  }
  
  function build(CPatient $patient) {
    parent::build($patient);
    
    $message = $event->message;
    
    $MSH = CHL7v2Segment::create("MSH", $message);
    $MSH->build($this);
    
    $EVN = CHL7v2Segment::create("EVN", $message);
    $EVN->planned_datetime = null;
    $EVN->occured_datetime = null;
    $EVN->build($this);
    
    $PID = CHL7v2Segment::create("PID", $message);
    $PID->patient = $patient;
    $PID->set_id  = 1;
    $PID->build($this);
    
    
  }
  
}

?>