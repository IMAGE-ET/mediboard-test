<?php

/**
 * Represents an HL7 QAK message segment (Query Parameter Definition) - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2SegmentQAK
 * QAK - Represents an HL7 QAK message segment (Query Parameter Definition)
 */

class CHL7v2SegmentQAK extends CHL7v2Segment {

  /** @var string */
  public $name   = "QAK";


  /** @var array */
  public $objects;

  /**
   * Build QPD segement
   *
   * @param CHL7v2Event $event Event
   *
   * @return null
   */
  function build(CHL7v2Event $event) {
    parent::build($event);

    $objects = $this->objects;

    // QAK-1: Query Tag (ST) (optional)
    $hl7_message_initiator = $event->message->_hl7_message_initiator;
    $QPD_request           = $hl7_message_initiator->getSegmentByName("QPD")->getStruct();

    $data[] = isset($QPD_request[1][0]) ? $QPD_request[1][0] : "PDQPDC_$event->code";

    // QAK-2: Query Response Status (ID) (optional)
    if ($objects == null) {
      $data[] = "AE";
    }
    elseif (count($objects) == 0) {
      $data[] = "NF";
    }
    else {
      $data[] = "OK";
    }

    // QAK-3: User Parameters (in successive fields) (Varies) (optional)
    $data[] = null;

    // QAK-4: Hit Count (NM) (optional)
    $data[] = null;

    // QAK-5: This payload (NM) (optional)
    $data[] = null;

    // QAK-6: Hits remaining (NM) (optional)
    $data[] = null;

    $this->fill($data);
  }
}