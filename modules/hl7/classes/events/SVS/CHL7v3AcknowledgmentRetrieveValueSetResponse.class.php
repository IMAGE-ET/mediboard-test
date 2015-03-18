<?php
/**
 * $Id: CHL7v3AcknowledgmentXDSb.class.php 26373 2014-12-12 13:37:57Z nicolasld $
 * 
 * @package    Mediboard
 * @subpackage hl7
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision: 26373 $
 */

/**
 * Class CHL7v3AcknowledgmentSVS
 * Acknowledgment SVS
 */
class CHL7v3AcknowledgmentRetrieveValueSetResponse extends CHL7v3AcknowledgmentSVS {
  /**
   * Get query ack
   *
   * @return array
   */
  function getQueryAck() {
    $dom = $this->dom;

    $prefix = $dom->documentElement->prefix;

    if ($prefix) {
      $prefix = "$prefix:";
    }

    $_value_set = $dom->queryNode($prefix."ValueSet");

    $value_set = new CHL7v3EventSVSValueSet($dom);
    $value_set->bind($dom, $_value_set, $prefix);

    return $value_set;
  }
}