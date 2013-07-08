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
 * Classe Association représentant l'association HasMember
 */
class CXDSHasMemberAssociation extends CXDSAssociation {

  /** @var CXDSSlot */
  public $submissionSetStatus;
  /** @var CXDSSlot */
  public $previousVersion;

  /**
   * Construction de l'instance
   *
   * @param String $id           String
   * @param String $sourceObject String
   * @param String $targetObject String
   * @param bool   $sign         false
   * @param String $status       String
   */
  function __construct($id, $sourceObject, $targetObject, $sign = false, $status = null) {
    parent::__construct($id, $status, $sourceObject, $targetObject);
    if ($sign) {
      $this->associationType = "urn:ihe:iti:2007:AssociationType:signs";
    }
  }

  /**
   * Setter SubmissionsetStatus
   *
   * @param String[] $value String[]
   *
   * @return void
   */
  function setSubmissionSetStatus($value) {
    $this->submissionSetStatus = new CXDSSlot("SubmissionSetStatus", $value);
  }

  /**
   * Setter PreviousVersion
   *
   * @param String[] $value String[]
   *
   * @return void
   */
  function setPreviousVersion($value) {
    $this->previousVersion = new CXDSSlot("PreviousVersion", $value);
    $this->setSubmissionSetStatus(array("Original"));
  }

  /**
   * Génération du xml
   *
   * @return CXDSXmlDocument
   */
  function toXML() {
    $xml = parent::toXML();
    if ($this->submissionSetStatus) {
      $xml->importDOMDocument($xml->documentElement, $this->submissionSetStatus->toXML());
    }
    if ($this->previousVersion) {
      $xml->importDOMDocument($xml->documentElement, $this->previousVersion->toXML());
    }
    return $xml;
  }
}