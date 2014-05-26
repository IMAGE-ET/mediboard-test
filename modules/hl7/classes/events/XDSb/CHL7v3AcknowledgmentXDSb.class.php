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
 * Class CHL7v3AcknowledgmentXDSb
 * Acknowledgment XDS
 */
class CHL7v3AcknowledgmentXDSb extends CHL7v3EventXDSb {
  public $acknowledgment;
  public $status;
  public $xpath;

  /**
   * Get acknowledgment status
   *
   * @return string
   */
  function getStatutAcknowledgment() {
    $dom = $this->dom;
    $this->xpath = $xpath = new CMbXPath($dom);
    $xpath->registerNamespace("rs", "urn:oasis:names:tc:ebxml-regrep:xsd:rs:3.0");
    $xpath->registerNamespace("rim", "urn:oasis:names:tc:ebxml-regrep:xsd:rim:3.0");
    $status = $xpath->queryAttributNode(".", null, "status");
    $this->status  = substr($status, strrpos($status, ":")+1);
    return $this->status;
  }

  /**
   * Get query ack
   *
   * @return string[]
   */
  function getQueryAck() {
    $xpath = $this->xpath;
    $status = $this->status;
    if ($status === "Failure") {
      $nodes = $xpath->query("//rs:RegistryErrorList/rs:RegistryError");
      $ack = array();
      foreach ($nodes as $_node) {
        $ack[] = array("status"  => $xpath->queryAttributNode(".", $_node, "codeContext"),
                       "context" => $xpath->queryAttributNode(".", $_node, "errorCode")
        );
      }
    }
    else {
      $nodes = $xpath->query("//rim:RegistryObjectList/rim:ObjectRef");
      if ($nodes && $nodes->length > 0) {
        $ack = array();
        foreach ($nodes as $_node) {
          $ack[] = array("status"  => $xpath->queryAttributNode(".", $_node, "id"),
                         "context" => "");
        }
      }
      else {
        $ack[] = array("status" => $status, "context" => "");
      }
    }

    return $ack;
  }
}