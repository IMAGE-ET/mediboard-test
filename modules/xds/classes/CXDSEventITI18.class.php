<?php

/**
 * $Id$
 *  
 * @category XDS
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */
 
/**
 * Description
 */
class CXDSEventITI18 {

  public $evenement_type = "registry";
  public $function = "DocumentRegistry_RegistryStoredQuery";
  public $ack_data;


  /**
   * Construit la requête
   *
   * @param String $data données nécessaires
   *
   * @return DOMElement
   */
  function build($data) {
    $xml = new CXDSXmlDocument();

    $message = $xml->createQueryElement($xml, "AdhocQueryRequest");
    $response_option = $xml->createQueryElement($message, "ResponseOption");
    $xml->addAttribute($response_option, "returnComposedObjects", "false");
    $xml->addAttribute($response_option, "returnType", "ObjectRef");
    $adhocQuery = $xml->createRimRoot("AdhocQuery", null, $message);
    $xml->addAttribute($adhocQuery, "id", "urn:uuid:5c4f972b-d56b-40ac-a5fc-c8ca9b40b9d4");
    $slot = new CXDSSlot("\$XDSDocumentEntryUniqueId", array("('$data')"));
    $xml->importDOMDocument($adhocQuery, $slot->toXML());
    //@todo voir pour chercher document archivé
    return $message;
  }

  /**
   * Retourne le message d'acquittement
   *
   * @return array
   */
  function getAcknowledgment() {

    $dom = new CMbXMLDocument("UTF-8");
    $dom->loadXMLSafe($this->ack_data);

    $xpath = new CMbXPath($dom);
    $xpath->registerNamespace("rs", "urn:oasis:names:tc:ebxml-regrep:xsd:rs:3.0");
    $xpath->registerNamespace("rim", "urn:oasis:names:tc:ebxml-regrep:xsd:rim:3.0");
    $status = $xpath->queryAttributNode(".", null, "status");

    if ($status === "urn:oasis:names:tc:ebxml-regrep:ResponseStatusType:Failure") {
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
      $ack = array();
      foreach ($nodes as $_node) {
        $ack[] = array("status"  => $xpath->queryAttributNode(".", $_node, "id"),
                       "context" => "");
      }
    }

    return $ack;
  }
}