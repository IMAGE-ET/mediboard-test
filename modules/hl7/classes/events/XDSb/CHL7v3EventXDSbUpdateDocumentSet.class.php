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
 * CHL7v3EventXDSbUpdateDocumentSet
 * Update document set
 */
class CHL7v3EventXDSbUpdateDocumentSet extends CHL7v3EventXDSb implements CHL7EventXDSbUpdateDocumentSet {
  /** @var string */
  public $interaction_id = "UpdateDocumentSet";

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
    $uuid = $this->uuid;

    $factory = new CCDAFactory($object);
    $cda = $factory->generateCDA();
    try {
      CCdaTools::validateCDA($cda);
    }
    catch (CMbException $e) {
      throw $e;
    }

    $xml = new CXDSXmlDocument();

    $xds = new CXDSMappingCDA($factory);
    $xds->type = $this->type;
    $header_xds = $xds->generateXDS57($uuid, $this->action);

    $xml->importDOMDocument($xml, $header_xds);

    $this->message = $xml->saveXML();

    $this->updateExchange(false);
  }
}
