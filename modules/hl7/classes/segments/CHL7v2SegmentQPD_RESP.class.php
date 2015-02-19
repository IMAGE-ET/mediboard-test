<?php

/**
 * Represents an HL7 QPD message segment (Query Parameter Definition) - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2SegmentQPD
 * QPD - Represents an HL7 QPD message segment (Query Parameter Definition)
 */

class CHL7v2SegmentQPD_RESP extends CHL7v2Segment {
  public $name   = "QPD_RESP";

  /**
   * Build QPD segement
   *
   * @param CHL7v2Event $event Event
   *
   * @return null
   */
  function build(CHL7v2Event $event) {
    parent::build($event);

    $hl7_message_initiator = $event->message->_hl7_message_initiator;

    /** @var CHL7v2SegmentQPD $QPD_request */
    $QPD_request           = $hl7_message_initiator->getSegmentByName("QPD");

    $this->fill($QPD_request->getStruct());
  }
}