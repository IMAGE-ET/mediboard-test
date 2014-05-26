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
 * CHL7v3EventXDSbProvideAndRegisterDocumentSetRequest
 * Provide and register document set request
 */
class CHL7v3EventXDSbProvideAndRegisterDocumentSetRequest
  extends CHL7v3EventXDSb implements CHL7EventXDSbProvideAndRegisterDocumentSetRequest {
  /** @var string */
  public $interaction_id = "ProvideAndRegisterDocumentSetRequest";
  public $_event_name    = "DocumentRepository_ProvideAndRegisterDocumentSet-b";
  public $old_version;
  public $old_id;
  public $type;
  public $uuid;
  public $hide;

  /**
   * Build ProvideAndRegisterDocumentSetRequest event
   *
   * @param CCompteRendu|CFile $object compte rendu
   *
   * @see parent::build()
   *
   * @throws CMbException
   * @return void
   */
  function build($object) {
    parent::build($object);

    $xml = new CXDSXmlDocument();

    $message = $xml->createDocumentRepositoryElement($xml, "ProvideAndRegisterDocumentSetRequest");

    $factory = new CCDAFactory($object);
    $factory->old_version = $this->old_version;
    $factory->old_id      = $this->old_id;
    $cda = $factory->generateCDA();
    try {
      CCdaTools::validateCDA($cda);
    }
    catch (CMbException $e) {
      throw $e;
    }

    $xds = new CXDSMappingCDA($factory);
    $xds->type = $this->type;
    $xds->doc_uuid = $this->uuid;
    switch ($this->hide) {
      case "hide_ps":
        $xds->hide_ps = true;
        break;

      case "hide_patient":
        $xds->hide_patient = true;
        break;

      default:
        $xds->hide_patient = false;
    }
    $header_xds = $xds->generateXDS41();
    $xml->importDOMDocument($message, $header_xds);

    //ajout d'un document
    $document = $xml->createDocumentRepositoryElement($message, "Document");
    $xml->addAttribute($document, "id", $xds->uuid["extrinsic"]);
    $document->nodeValue = base64_encode($cda);

    //ajout de la signature
    CEAIHandler::notify("AfterBuild", $this, $xml, $factory, $xds);

    $this->message = $xml->saveXML($message);
    $this->updateExchange(false);
  }
}