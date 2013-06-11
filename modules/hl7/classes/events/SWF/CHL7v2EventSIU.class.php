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
 * Classe CHL7v2EventSIU
 * Scheduled Workflow
 */
class CHL7v2EventSIU extends CHL7v2Event implements CHL7EventSIU {

  /** @var string */
  public $event_type = "SIU";

  /**
   * Construct
   *
   * @return \CHL7v2EventSIU
   */
  function __construct() {
    parent::__construct();
    
    $this->profil    = "SWF";
    $this->msg_codes = array ( 
      array(
        $this->event_type, $this->code, "{$this->event_type}_{$this->code}"
      )
    );
    $this->transaction = CIHE::getSWFTransaction($this->code);
  }

  /**
   * Build event
   *
   * @param CMbObject $object Object
   *
   * @see parent::build()
   *
   * @return void
   */
  function build($object) {
    parent::build($object);
        
    // Message Header 
    $this->addMSH();
  }

  /**
   * MSH - Represents an HL7 MSH message segment (Message Header)
   *
   * @return void
   */
  function addMSH() {
    $MSH = CHL7v2Segment::create("MSH", $this->message);
    $MSH->build($this);
  }
  
  /**
   * SCH - Represents an HL7 SCH segment (Scheduling Activity Information)
   *
   * @param CConsultation $appointment Appointment
   *
   * @return void
   */
  function addSCH(CConsultation $appointment) {
    $SCH = CHL7v2Segment::create("SCH", $this->message);
    $SCH->appointment = $appointment;
    $SCH->build($this);
  }
  
  /**
   * Represents an HL7 PID message segment (Patient Identification)
   *
   * @param CPatient $patient Patient
   *
   * @return void
   */
  function addPID(CPatient $patient) {
    $PID = CHL7v2Segment::create("PID", $this->message);
    $PID->patient = $patient;
    $PID->set_id  = 1;
    $PID->build($this);
  }
  
  /**
   * Represents an HL7 PD1 message segment (Patient Additional Demographic)
   *
   * @param CPatient $patient Patient
   *
   * @return void
   */
  function addPD1(CPatient $patient) {
    $PD1 = CHL7v2Segment::create("PD1", $this->message);
    $PD1->patient = $patient;
    $PD1->build($this);
  }
  
  /**
   * Represents an HL7 PV1 message segment (Patient Visit)
   *
   * @return void
   */
  function addPV1() {
    $PV1 = CHL7v2Segment::create("PV1", $this->message);
    $PV1->build($this);
  }
  
  /**
   * RGS - Represents an HL7 SCH segment (Resource Group)
   *
   * @param CConsultation $appointment Appointment
   * @param int           $set_id      Set ID
   *
   * @return void
   */
  function addRGS(CConsultation $appointment, $set_id = 1) {
    $RGS = CHL7v2Segment::create("RGS", $this->message);
    $RGS->set_id = $set_id;
    $RGS->appointment = $appointment;
    $RGS->build($this);
  }
  
  /**
   * AIG - Represents an HL7 SCH segment (Appointment Information - General Resource)
   *
   * @param CConsultation $appointment Appointment
   * @param int           $set_id      Set ID
   *
   * @return void
   */
  function addAIG(CConsultation $appointment, $set_id = 1) {
    $AIG = CHL7v2Segment::create("AIG", $this->message);
    $AIG->set_id = $set_id;
    $AIG->appointment = $appointment;
    $AIG->build($this);
  }
  
  /**
   * AIL - Represents an HL7 AIL segment (Appointment Information - Location Resource)
   *
   * @param CConsultation $appointment Appointment
   * @param int           $set_id      Set ID
   *
   * @return void
   */
  function addAIL(CConsultation $appointment, $set_id = 1) {
    $AIL = CHL7v2Segment::create("AIL", $this->message);
    $AIL->set_id = $set_id;
    $AIL->appointment = $appointment;
    $AIL->build($this);
  }
}