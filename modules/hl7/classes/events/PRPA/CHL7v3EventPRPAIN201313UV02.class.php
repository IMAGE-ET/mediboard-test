<?php

/**
 * Patient Registry Patient Not Added
 *
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v3EventPRPAIN201313UV02
 * Patient Registry Patient Not Added
 */
class CHL7v3EventPRPAIN201313UV02 extends CHL7v3AcknowledgmentPRPA implements CHL7EventPRPAST201317UV02 {
  /** @var string */
  public $interaction_id = "IN201313UV02";
  public $queryAck;
  public $subject;

  /**
   * Get interaction
   *
   * @return string|void
   */
  function getInteractionID() {
    return "{$this->event_type}_{$this->interaction_id}";
  }

  /**
   * Get acknowledgment status
   *
   * @return string
   */
  function getStatutAcknowledgment() {
    $dom = $this->dom;

    $this->acknowledgment = $dom->queryNode("//hl7:".$this->getInteractionID()."/hl7:acknowledgement");

    return $dom->getValueAttributNode($this->acknowledgment, "typeCode");
  }

  /**
   * Get query ack
   *
   * @return string
   */
  function getQueryAck() {

  }
}