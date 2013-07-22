<?php
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage hl7
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

/**
 * Class CHL7v3AcknowledgmentPRPA
 * Acknowledgment HL7v3
 */
class CHL7v3AcknowledgmentPRPA extends CHL7v3EventPRPA {
  public $acknowledgment;

  /**
   * Get acknowledgment status
   *
   * @return string
   */
  function getStatutAcknowledgment() {
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
  }
}
