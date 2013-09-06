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
 * Classe de l'evenement ITI57
 */
class CXDSEventITI57 {

  public $evenement_type = "registry";
  public $function = "DocumentRegistry_UpdateDocumentSet";
  public $ack_data;
  public $uuid;
  public $archivage;
  public $depublication;


  /**
   * Création du message SOAP pour l'autorisation
   *
   * @param String $data String
   *
   * @return DOMElement
   */
  function build ($data) {
    $xml = new CXDSXmlDocument();

    $xds = new CXDSMappingCDA($data);
    $header_xds = $xds->generateXDS57($this->uuid, $this->archivage, $this->depublication);

    $xml->importDOMDocument($xml, $header_xds);

    return $xml->documentElement;
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
    $xpath->registerNamespace("rs", "urn:oasis:names:tc:ebxml-regrep:xsd:rs:3.0");
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
      $ack[] = array("status" => $status, "context" => "");
    }

    return $ack;
  }
}
