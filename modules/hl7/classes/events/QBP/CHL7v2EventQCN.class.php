<?php

/**
 * Patient Demographics Query Cancel Query HL7
 *
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2EventQCN
 * Patient Demographics Query Cancel Query
 */
class CHL7v2EventQCN extends CHL7v2Event implements CHL7EventQCN {

  /** @var string */
  public $event_type = "QCN";

  /**
   * Construct
   *
   * @return \CHL7v2EventQCN
   */
  function __construct() {
    parent::__construct();

    $this->profil      = "PDQ";
    $this->msg_codes   = array (
      array(
        $this->event_type, $this->code, "{$this->event_type}_{$this->code}"
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
}