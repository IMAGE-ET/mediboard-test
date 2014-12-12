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
  /** @var  CMbXPath */
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

  /**
   * Return the result count
   *
   * @return string
   */
  function getResultCount() {
    return $this->xpath->queryAttributNode(".", null, "totalResultCount");
  }

  /**
   * Get the entryUUID of document in the association
   *
   * @return String[]
   */
  function getDocumentUUIDAssociation() {
    $xpath     = $this->xpath;
    $nodes     = $xpath->query("//rim:Association");
    $entryUUID = array();

    foreach ($nodes as $_node) {
      $type = $xpath->queryAttributNode(".", $_node, "associationType");

      if ($type != "urn:oasis:names:tc:ebxml-regrep:AssociationType:HasMember") {
        continue;
      }
      $entryUUID[] = $xpath->queryAttributNode(".", $_node, "targetObject");
    }

    return $entryUUID;
  }

  /**
   * Return the extrinsic object
   *
   * @return DOMElement[]
   */
  function getDocuments() {
    $xpath     = $this->xpath;
    $nodes     = $xpath->query("//rim:ExtrinsicObject");

    $documents = array();
    foreach ($nodes as $_node) {
      $documents[] = $_node;
    }

    return $documents;
  }
}