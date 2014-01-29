<?php

/**
 * Order Message HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Classe CHL7v2EventORM
 * Order Message
 */
class CHL7v2EventORM extends CHL7v2Event implements CHL7EventORM {

  /** @var string */
  public $event_type = "ORM";

  /**
   * Construct
   *
   * @return \CHL7v2EventORM
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
   * Represents an HL7 PID message segment (Patient Identification)
   *
   * @param CPatient $patient Patient
   * @param CSejour  $sejour  Admit
   *
   * @return void
   */
  function addPID(CPatient $patient, CSejour $sejour = null) {
    $segment_name = $this->_is_i18n ? "PID_FR" : "PID";
    $PID = CHL7v2Segment::create($segment_name, $this->message);
    $PID->patient = $patient;
    $PID->sejour = $sejour;
    $PID->set_id  = 1;
    $PID->build($this);
  }

  /**
   * Represents an HL7 PV1 message segment (Patient Visit)
   *
   * @param CSejour $sejour Admit
   * @param int     $set_id Set ID
   *
   * @return void
   */
  function addPV1(CSejour $sejour = null, $set_id = 1) {
    $segment_name = $this->_is_i18n ? "PV1_FR" : "PV1";
    $PV1          = CHL7v2Segment::create($segment_name, $this->message);
    $PV1->sejour  = $sejour;
    $PV1->set_id  = $set_id;
    if ($sejour) {
      $PV1->curr_affectation = $sejour->_ref_hl7_affectation;
    }
    $PV1->build($this);
  }

  /**
   * Represents an HL7 ORC message segment (Common order)
   *
   * @param CMbObject $object object
   *
   * @return void
   */
  function addORC($object) {
    $ORC = CHL7v2Segment::create("ORC", $this->message);
    $ORC->object = $object;
    $ORC->build($this);
  }

  /**
   * Represents an HL7 OBR message segment (Observation Request)
   *
   * @param CMbObject $object object
   *
   * @return void
   */
  function addOBR($object) {
    $OBR = CHL7v2Segment::create("OBR", $this->message);
    $OBR->object = $object;
    $OBR->build($this);
  }

  /**
   * Represents an HL7 ZDS message segment ()
   *
   * @return void
   */
  function addZDS() {
    $ZDS = CHL7v2Segment::create("ZDS", $this->message);
    $ZDS->build($this);
  }
}