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
 * Classe ExternalIdentifier représentant la variable PatientId
 */
class CXDSPatientID extends CXDSExternalIdentifier {

  /**
   * Construction de l'instance
   *
   * @param String $id             String
   * @param String $registryObject String
   * @param String $value          String
   * @param bool   $registry       false
   */
  function __construct($id, $registryObject, $value, $registry = false) {
    parent::__construct($id, $registry, $value);
    $this->identificationScheme = "urn:uuid:58a6f841-87b3-4a3e-92fd-a8ffeff98427";
    if ($registry) {
      $this->identificationScheme = "urn:uuid:6b5aea1a-874d-4603-a4bc-96a0a7b38446";
    }
    $this->name = new CXDSName("XDSSumissionSet.patientId");
  }
}