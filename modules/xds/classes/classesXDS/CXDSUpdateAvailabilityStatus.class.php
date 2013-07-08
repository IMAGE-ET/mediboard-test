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
 * classe Association représentant l'association UpdateAvailabilityStatus
 */
class CXDSUpdateAvailabilityStatus extends CXDSAssociation {

  /** @var CXDSSlot */
  public $newStatus;
  /** @var CXDSSlot */
  public $originalStatus;

  /**
   * @see parent::__construct()
   */
  function __construct($id, $status, $sourceObject, $targetObject) {
    parent::__construct($id, $status, $sourceObject, $targetObject);
    $this->associationType = "urn:ihe:iti:2010:AssociationType:UpdateAvailabilityStatus";
  }

  /**
   * Setter NewStatus
   *
   * @param String[] $value String[]
   *
   * @return void
   */
  function setNewStatus($value) {
    $this->newStatus = new CXDSSlot("NewStatus", $value);
  }

  /**
   * Setter originalStatus
   *
   * @param String[] $value String[]
   *
   * @return void
   */
  function setOriginalStatus($value) {
    $this->originalStatus = new CXDSSlot("OriginalStatus", $value);
  }

  /**
   * @see parent::toXML()
   */
  function toXML() {
    $xml = parent::toXML();
    if ($this->newStatus) {
      $xml->importDOMDocument($xml->documentElement, $this->newStatus->toXML());
    }
    if ($this->originalStatus) {
      $xml->importDOMDocument($xml->documentElement, $this->originalStatus->toXML());
    }
    return $xml;
  }
}
