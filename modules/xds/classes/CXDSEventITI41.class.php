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
 * Classe de l'evenement ITI41
 */
class CXDSEventITI41 {

  public $evenement_type = "producer";
  public $function = "DocumentRepository_ProvideAndRegisterDocumentSet-b";
  public $ack_data;


  /**
   * Création du message SOAP pour l'autorisation
   *
   * @param String $data String
   *
   * @return DOMElement
   */
  function build ($data) {
    $xml = new CXDSXmlDocument();

    $message = $xml->createDocumentRepositoryElement($xml, "ProvideAndRegisterDocumentSetRequest");

    $xds = new CXDSMappingCDA($data);
    $header_xds = $xds->generateXDS();
    $xml->importDOMDocument($message, $header_xds);

    $document_cda = new CCDADomDocument();
    $document_cda->loadXML($data);

    //ajout d'un document
    $document = $xml->createDocumentRepositoryElement($message, "Document");
    $xml->addAttribute($document, "id", "2.25.4896.4");
    $document->nodeValue = base64_encode($document_cda->saveXML());

    //ajout de la signature
    $dsig = new CDSIGTools($xml, CAppUI::conf("dmp path_certificat"), CAppUI::conf("dmp passphrase_certificat"));
    $dsig_signature = $dsig->createSignatureLot($xds->oid, $document_cda);
    $signature = $xml->createDocumentRepositoryElement($message, "Document");
    $xml->addAttribute($signature, "id", "2.25.4896.3");
    $signature->nodeValue = base64_encode($dsig_signature->saveXML());

    return $message;
  }

  /**
   * Retourne le message d'acquittement
   *
   * @return array
   */
  function getAcknowledgment() {

    $dom = new CMbXMLDocument();
    $dom->loadXMLSafe($this->ack_data);

    $xpath = new CMbXPath($dom);
    $xpath->registerNamespace("ns1", "urn:oasis:names:tc:ebxml-regrep:xsd:rs:3.0");
    $status = $xpath->queryAttributNode(".", null, "status");

    if ($status === "urn:oasis:names:tc:ebxml-regrep:ResponseStatusType:Failure") {
      $nodes = $xpath->query("//ns1:RegistryErrorList/ns1:RegistryError");
      $ack = array();
      foreach ($nodes as $_node) {
        $ack[] = array("status"  => $xpath->queryAttributNode(".", $_node, "codeContext"),
                       "context" => $xpath->queryAttributNode(".", $_node, "errorCode")
        );
      }
    }
    else {
      $ack[] = array("status" => $status, "context" => "");
    }

    return $ack;
  }
}