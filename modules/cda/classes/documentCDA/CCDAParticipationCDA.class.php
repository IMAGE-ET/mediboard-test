<?php

/**
 * $Id$
 *  
 * @category CDA
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */
 
/**
 * Classe regroupant les fonction de type Participation
 */
class CCDAParticipationCDA extends CCDADocumentCDA {

  /**
   * Création de l'auteur
   *
   * @return CCDAPOCD_MT000040_Author
   */
  function setAuthor() {
    $docItem = parent::$docItem;
    $author = new CCDAPOCD_MT000040_Author();
    $object = $docItem->loadTargetObject();
    $praticien = "";
    switch (get_class($object)) {
      case "CSejour":
        /** @var CSejour $object CSejour*/
        $praticien = $object->loadRefPraticien();
        break;
      case "COperation":
        /** @var COperation $object COperation*/
        $praticien = $object->loadRefChir();
        break;
      case "CConsultation":
        /** @var CConsultation $object CConsultation*/
        $praticien = $object->loadRefPraticien();
        break;
    }
    $date = $this->getTimeToUtc($docItem->_ref_last_log->date);
    $ts = new CCDATS();
    $ts->setValue($date);
    $author->setTime($ts);
    $author->setAssignedAuthor(parent::$role->setAssignedAuthor($praticien));
    return $author;
  }

  /**
   * Création d'un custodian
   *
   * @return CCDAPOCD_MT000040_Custodian
   */
  function setCustodian() {
    $custodian = new CCDAPOCD_MT000040_Custodian();

    $custodian->setAssignedCustodian(parent::$role->setAssignedCustodian());
    return $custodian;
  }

  /**
   * Création du recordTarget
   *
   * @return CCDAPOCD_MT000040_Recordtarget
   */
  function setRecordTarget() {
    $record = new CCDAPOCD_MT000040_RecordTarget();
    $record->setPatientRole(parent::$role->setPatientRole());
    return $record;
  }

  /**
   * Création d'un legalAuthenticator
   *
   * @return CCDAPOCD_MT000040_LegalAuthenticator
   */
  function setLegalAuthenticator() {
    $docItem = parent::$docItem;
    $legalAuthenticator = new CCDAPOCD_MT000040_LegalAuthenticator();
    $date = $this->getTimeToUtc(CMbDT::dateTime());
    $ts = new CCDATS();
    $ts->setValue($date);
    $legalAuthenticator->setTime($ts);
    $cs = new CCDACS();
    $cs->setCode("S");
    $legalAuthenticator->setSignatureCode($cs);
    $object = $docItem->loadTargetObject();
    $praticien = "";
    switch (get_class($object)) {
      case "CSejour":
        /** @var CSejour $object CSejour*/
        $praticien = $object->loadRefPraticien();
        break;
      case "COperation":
        /** @var COperation $object COperation*/
        $praticien = $object->loadRefChir();
        break;
      case "CConsultation":
        /** @var CConsultation $object CConsultation*/
        $praticien = $object->loadRefPraticien();
        break;
    }
    $legalAuthenticator->setAssignedEntity(parent::$role->setAssignedEntity($praticien));
    return $legalAuthenticator;
  }

  /**
   * Création de la location
   *
   * @param CUser|CMediUsers $user CUser|CMediUsers
   *
   * @return CCDAPOCD_MT000040_Location
   */
  function setLocation($user) {
    $location = new CCDAPOCD_MT000040_Location();

    $location->setHealthCareFacility(parent::$role->setHealthCareFacility($user));

    return $location;
  }
}