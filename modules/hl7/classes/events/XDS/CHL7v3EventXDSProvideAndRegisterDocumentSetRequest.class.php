<?php

/**
 * $Id$
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */
 
/**
 * CHL7v3EventXDSProvideAndRegisterDocumentSetRequest
 * Provide and register document set request
 */
class CHL7v3EventXDSProvideAndRegisterDocumentSetRequest
  extends CHL7v3EventXDS implements CHL7EventXDSProvideAndRegisterDocumentSetRequest {
  /** @var string */
  public $interaction_id = "ProvideAndRegisterDocumentSetRequest";

  /**
   * Build ProvideAndRegisterDocumentSetRequest event
   *
   * @param CCompteRendu|CFile $object compte rendu
   *
   * @see parent::build()
   *
   * @return void
   */
  function build($object) {
    parent::build($object);

    $xml = new CXDSXmlDocument();

    $message = $xml->createDocumentRepositoryElement($xml, "ProvideAndRegisterDocumentSetRequest");

    $factory = new CCDAFactory($object);
    $cda = $factory->generateCDA();
    $xds = new CXDSMappingCDA($factory);
    $header_xds = $xds->generateXDS41();
    $xml->importDOMDocument($message, $header_xds);

    //ajout d'un document
    $document = $xml->createDocumentRepositoryElement($message, "Document");
    $xml->addAttribute($document, "id", $xds->uuid["extrinsic"]);
    $document->nodeValue = base64_encode($cda);

    //ajout de la signature
    $dsig = new CDSIG($xml, $this->path_certificate, $this->passphrase_certificate);
    $dsig_signature = $dsig->createSignatureLot($xds->oid, $factory->dom_cda);
    $signature = $xml->createDocumentRepositoryElement($message, "Document");
    $xml->addAttribute($signature, "id", $xds->uuid["signature"]);
    $signature->nodeValue = base64_encode($dsig_signature->saveXML());

    $this->message = $xml->saveXML($message);
    $this->updateExchange(false);
  }
}
