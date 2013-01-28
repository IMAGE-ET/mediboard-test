<?php

/**
 * Patient Demographics Query HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2EventPDQ
 * Patient Demographics Query
 */
class CHL7v2EventQBP extends CHL7v2Event implements CHL7EventQBP {
  /**
   * @var string
   */
  var $event_type = "QBP";

  /**
   * Construct
   *
   * @return \CHL7v2EventQBP
   */
  function __construct() {
    parent::__construct();
    
    $this->profil      = "PDQ";
    $this->msg_codes   = array ( 
      array(
        $this->event_type, $this->code, "{$this->event_type}_{$this->struct_code}"
      )
    );

    $this->transaction = CPDQ::getPDQTransaction($this->code);
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

    $this->addMSH($object);
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
   * QPD - Represents an HL7 QPD message segment (Query Parameter Definition)
   *
   * @param CPatient $patient Patient
   *
   * @return void
   */
  function addQPD($patient) {
    $QPD = CHL7v2Segment::create("QPD", $this->message);
    $QPD->patient = $patient;
    $QPD->build($this);
  }

  /**
   * RCP - Represents an HL7 RCP message segment (Response Control Parameter)
   *
   * @param CPatient $patient Patient
   *
   * @return void
   */
  function addRCP($patient) {
    $RCP = CHL7v2Segment::create("RCP", $this->message);
    $RCP->patient = $patient;
    $RCP->build($this);
  }

  /**
   * RCP - Represents an HL7 DSC message segment (Continuation Pointer)
   *
   * @param CPatient $patient Patient
   *
   * @return void
   */
  function addDSC($patient) {
    $DSC = CHL7v2Segment::create("DSC", $this->message);
    $DSC->patient = $patient;
    $DSC->build($this);
  }
}