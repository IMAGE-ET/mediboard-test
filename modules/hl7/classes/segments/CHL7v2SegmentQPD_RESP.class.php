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
  var $name   = "QPD_RESP";

  /**
   * @var CPatient
   */
  var $patient = null;

  /**
   * @var CSejour
   */
  var $sejour = null;

  /**
   * @var CAffectation
   */
  var $affectation = null;

  /**
   * Build QPD segement
   *
   * @param CHL7v2Event $event Event
   *
   * @return null
   */
  function build(CHL7v2Event $event) {
    parent::build($event);

    $patient = $this->patient;
    $sejour  = $this->sejour;

    $exchange_ihe = $event->_exchange_ihe;
    $message      = $exchange_ihe->_message;

    $hl7_message = new CHL7v2Message();
    $hl7_message->parse($message);

    $QPD_request = $hl7_message->getSegmentByName("QPD");

    $this->fill($QPD_request->getStruct());
  }
}