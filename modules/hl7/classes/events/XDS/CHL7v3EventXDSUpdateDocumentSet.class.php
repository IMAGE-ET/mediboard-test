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
 * CHL7v3EventXDSUpdateDocumentSet
 * Update document set
 */
class CHL7v3EventXDSUpdateDocumentSet extends CHL7v3EventXDS implements CHL7EventXDSUpdateDocumentSet {
  /** @var string */
  public $interaction_id = "UpdateDocumentSet";

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
    $uuid      = $this->uuid;
    $archived  = $this->archived;
    $unpublish = $this->unpublish;

    $factory = new CCDAFactory($object);
    $factory->generateCDA();

    $xml = new CXDSXmlDocument();

    $xds = new CXDSMappingCDA($factory);
    $header_xds = $xds->generateXDS57($uuid, $archived, $unpublish);

    $xml->importDOMDocument($xml, $header_xds);

    $this->message = $xml->saveXML();

    $this->updateExchange(false);
  }
}
