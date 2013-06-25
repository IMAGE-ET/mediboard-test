<?php

/**
 * Patient Registry Get Demographics Query Response
 * A patient registry responds to a query with demographic information in the registry for the patient specified in the query
 *
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v3EventPRPAIN201308UV02
 * Patient Registry Get Demographics Query Response
 */
class CHL7v3EventPRPAIN201308UV02 extends CHL7v3AcknowledgmentPRPA implements CHL7EventPRPAST201317UV02 {
  /** @var string */
  public $interaction_id = "IN201308UV02";
  public $queryAck;

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
    //Valeur fixée à "AA"
    return "AA";
  }

  /**
   * Get query ack
   *
   * @return string
   */
  function getQueryAck() {
    $dom = $this->dom;

    $this->queryAck = $dom->queryNode("//hl7:".$this->getInteractionID()."/hl7:controlActProcess/hl7:queryAck");

    $queryResponseCode = $dom->queryNode("hl7:queryResponseCode", $this->queryAck);

    return $dom->getValueAttributNode($queryResponseCode, "code");
  }
}