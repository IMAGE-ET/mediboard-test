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
  public $acknowledgment;
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
   * Get acknowledgment text
   *
   * @return string
   */
  function getTextAcknowledgment() {
    $dom = $this->dom;

    $acknowledgementDetail = $dom->queryNode("hl7:acknowledgementDetail", $this->acknowledgment);

    return $dom->queryTextNode("hl7:text", $acknowledgementDetail);
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

  /**
   * Get status code
   *
   * @return string
   */
  function getStatusCode() {
    $dom = $this->dom;

    $interaction_id = "//hl7:".$this->getInteractionID();

    $this->subject = $dom->queryNode("$interaction_id/hl7:controlActProcess/hl7:subject");

    $patient = $dom->queryNode("hl7:registrationEvent/hl7:subject1/hl7:patient", $this->subject);

    $statusCode = $dom->queryNode("hl7:statusCode", $patient);

    return $dom->getValueAttributNode($statusCode, "code");
  }

  /**
   * Get closing date
   *
   * @return string
   */
  function getDateFermeture() {
    $dom = $this->dom;

    $effectiveTime = $dom->queryNode("hl7:registrationEvent/hl7:effectiveTime", $this->subject);

    if (!$effectiveTime) {
      return null;
    }

    return $this->setUtcToTime($dom->getValueAttributNode($effectiveTime, "value"));
  }

  /**
   * Get closing pattern
   *
   * @return string
   */
  function getMotifFermeture() {
    $dom = $this->dom;

    $interaction_id = "//hl7:".$this->getInteractionID();

    $code = $dom->queryNode($interaction_id."/hl7:controlActProcess/hl7:reasonOf/hl7:detectedIssueEvent/hl7:code");

    $reasonOf = $dom->getValueAttributNode($code, "code");

    $reasonOf = $reasonOf ? $reasonOf : "none";

    return CAppUI::tr("DMP-reasonOf_$reasonOf");
  }

  /**
   * Get authorization status
   *
   * @return string
   */
  function getAuthorizationStatus() {
    $dom = $this->dom;

    $interaction_id = "//hl7:".$this->getInteractionID();

    $value = $dom->queryNode($interaction_id."/hl7:attentionLine[hl7:keyWordText[@code='AUTORISATION']]/hl7:value");

    return $dom->getValueAttributNode($value, "code");
  }

  /**
   * Get status doctor
   *
   * @return string
   */
  function getStatusMT() {
    $dom = $this->dom;

    $interaction_id = "//hl7:".$this->getInteractionID();

    $value = $dom->queryNode($interaction_id."/hl7:attentionLine[hl7:keyWordText[@code='STATUT_MT']]/hl7:value");

    if (!$value) {
      return;
    }

    return $dom->getValueAttributNode($value, "value");
  }
}