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
 * La classe Association XDS represente les associations entre fiches
 * ou entre lot de soumission et fiche
 */
class CXDSAssociation extends CXDSRegistryObject {

  public $associationType;
  public $sourceObject;
  public $targetObject;
  /** @var  CXDSSlot */
  public $OriginalStatus;
  /** @var  CXDSSlot */
  public $NewStatus;
  /** @var  CXDSSlot */
  public $SubmissionSetStatus;
  /** @var  CXDSSlot */
  public $PreviousVersion;

  /**
   * Construction de l'instance
   *
   * @param String $id              Identifiant
   * @param String $sourceObject    Source
   * @param String $targetObject    Cible
   * @param String $associationType Association de type remplacement
   */
  function __construct($id, $sourceObject, $targetObject, $associationType = null) {
    parent::__construct($id);
    $this->associationType = "urn:oasis:names:tc:ebxml-regrep:AssociationType:HasMember";
    if ($associationType) {
      $this->associationType = $associationType;
    }
    $this->sourceObject = $sourceObject;
    $this->targetObject = $targetObject;
    $this->objectType = "urn:oasis:names:tc:ebxml-regrep:ObjectType:RegistryObject:Association";
  }

  /**
   * @see parent::toXML()
   *
   * @return CXDSXmlDocument
   */
  function toXML() {
    $xml = new CXDSXmlDocument();
    $root = $xml->createAssociationRoot($this->id, $this->associationType, $this->sourceObject, $this->targetObject, $this->objectType);

    if ($this->OriginalStatus) {
      $xml->importDOMDocument($root, $this->OriginalStatus->toXML());
    }

    if ($this->NewStatus) {
      $xml->importDOMDocument($root, $this->NewStatus->toXML());
    }

    if ($this->SubmissionSetStatus) {
      $xml->importDOMDocument($root, $this->SubmissionSetStatus->toXML());
    }

    if ($this->PreviousVersion) {
      $xml->importDOMDocument($root, $this->PreviousVersion->toXML());
    }

    return $xml;
  }
}