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
class CHL7v3EventXDSbRetrieveDocumentSet extends CHL7v3EventXDSb implements CHL7EventXDSbRetrieveDocumentSet {
  /** @var string */
  public $interaction_id = "RetrieveDocumentSet";

  public $repository_id;
  public $oid;

  /**
   * Build RetrieveDocumentSetRequest event
   *
   * @param CPatient $object Patient
   *
   * @see parent::build()
   *
   * @throws CMbException
   * @return void
   */
  function build($object) {
    parent::build($object);

    $xml = new CXDSXmlDocument();

    $root             = $xml->createDocumentRepositoryElement($xml, "RetrieveDocumentSetRequest");
    $document_request = $xml->createDocumentRepositoryElement($root, "DocumentRequest");

    $xml->createDocumentRepositoryElement($document_request, "RepositoryUniqueId", $this->repository_id);
    $xml->createDocumentRepositoryElement($document_request, "DocumentUniqueId"  , $this->oid);

    $this->message = $xml->saveXML();

    $this->updateExchange(false);
  }
}
