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
class CHL7v2EventPDQ extends CHL7v2Event implements CHL7EventPDQ {
  var $event_type = "QBP";
  
  function __construct() {
    parent::__construct();
    
    $this->profil      = "PDQ";
    $this->msg_codes   = array ( 
      array(
        $this->event_type, $this->code
      )
    );

    $this->transaction = CPDQ::getPDQTransaction($this->code);
  }
  
  /**
   * @see parent::build()
   */
  function build($object) {
    parent::build($object);

    $this->addMSH($object);
  }

  /**
   * MSH - Represents an HL7 MSH message segment (Message Header)
   */
  function addMSH() {
    $MSH = CHL7v2Segment::create("MSH", $this->message);
    $MSH->build($this);
  }

  /**
   * QPD - Represents an HL7 QPD message segment (Query Parameter Definition)
   */
  function addQPD() {
    $QPD = CHL7v2Segment::create("QPD", $this->message);
    $QPD->build($this);
  }

  /**
   * RCP - Represents an HL7 RCP message segment (Response Control Parameter)
   */
  function addRCP() {
    $RCP = CHL7v2Segment::create("RCP", $this->message);
    $RCP->build($this);
  }
}

?>