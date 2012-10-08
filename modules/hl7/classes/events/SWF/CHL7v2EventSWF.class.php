<?php

/**
 * Scheduled Workflow HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Classe CHL7v2EventSWF 
 * Scheduled Workflow
 */
class CHL7v2EventSWF extends CHL7v2Event implements CHL7EventSWF {
  var $event_type = "SIU";
  
  function __construct() {
    parent::__construct();
    
    $this->profil    = "SWF";
    $this->msg_codes = array ( 
      array(
        $this->event_type, $this->code, "{$this->event_type}_{$this->struct_code}"
      )
    );
    $this->transaction = CIHE::getSWFTransaction($this->code);
  }
  
  /**
   * @see parent::build()
   */
  function build($object) {
    parent::build($object);
        
    // Message Header 
    $this->addMSH();
  }

  /**
   * MSH - Represents an HL7 MSH message segment (Message Header) 
   */
  function addMSH() {
    $MSH = CHL7v2Segment::create("MSH", $this->message);
    $MSH->build($this);
  }
  
  /**
   * SCH - Represents an HL7 SCH segment (Scheduling Activity Information) 
   */
  function addSCH(CMbObject $scheduling) {
    $SCH = CHL7v2Segment::create("SCH", $this->message);
    $SCH->build($this);
  }
  
  /**
   * Represents an HL7 PID message segment (Patient Identification)
   * @param CPatient Patient
   */
  function addPID(CPatient $patient) {
    $PID = CHL7v2Segment::create("PID", $this->message);
    $PID->patient = $patient;
    $PID->set_id  = 1;
    $PID->build($this);
  }
  
  /**
   * Represents an HL7 PD1 message segment (Patient Additional Demographic)
   * @param CPatient Patient
   */
  function addPD1(CPatient $patient) {
    $PD1 = CHL7v2Segment::create("PD1", $this->message);
    $PD1->patient = $patient;
    $PD1->build($this);
  }
  
  /**
   * Represents an HL7 PV1 message segment (Patient Visit)
   * @param CSejour Admit
   */
  function addPV1(CMbObject $scheduling = null, $set_id = 1) {    
    $PV1 = CHL7v2Segment::create("PV1", $this->message);
    $PV1->sejour = null;
    $PV1->set_id = 1;
    $PV1->build($this);
  }
  
  /**
   * RGS - Represents an HL7 SCH segment (Resource Group) 
   */
  function addRGS(CMbObject $scheduling) {
    $RGS = CHL7v2Segment::create("RGS", $this->message);
    $RGS->build($this);
  }
  
  /**
   * AIG - Represents an HL7 SCH segment (Appointment Information - General Resource) 
   */
  function addAIG(CMbObject $scheduling) {
    $AIG = CHL7v2Segment::create("AIG", $this->message);
    $AIG->build($this);
  }
  
  /**
   * AIL - Represents an HL7 AIL segment (Appointment Information - Location Resource) 
   */
  function addAIL(CMbObject $scheduling) {
    $AIL = CHL7v2Segment::create("AIL", $this->message);
    $AIL->build($this);
  }
}

?>