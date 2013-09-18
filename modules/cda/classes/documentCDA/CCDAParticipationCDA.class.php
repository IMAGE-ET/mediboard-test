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
    $author = new CCDAPOCD_MT000040_Author();

    $date = $this->getTimeToUtc(self::$cda_factory->date_author);
    $ts = new CCDATS();
    $ts->setValue($date);
    $author->setTime($ts);
    $author->setAssignedAuthor(parent::$role->setAssignedAuthor());
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
    $legalAuthenticator = new CCDAPOCD_MT000040_LegalAuthenticator();
    $date = $this->getTimeToUtc(CMbDT::dateTime());
    $ts = new CCDATS();
    $ts->setValue($date);
    $legalAuthenticator->setTime($ts);
    $cs = new CCDACS();
    $cs->setCode("S");
    $legalAuthenticator->setSignatureCode($cs);
    $praticien = self::$cda_factory->practicien;
    $legalAuthenticator->setAssignedEntity(parent::$role->setAssignedEntity($praticien));
    return $legalAuthenticator;
  }

  /**
   * Création de la location
   *
   * @return CCDAPOCD_MT000040_Location
   */
  function setLocation() {
    $location = new CCDAPOCD_MT000040_Location();

    $location->setHealthCareFacility(parent::$role->setHealthCareFacility());

    return $location;
  }

  /**
   * Création du performer
   *
   * @param CMediusers $praticien praticien
   *
   * @return CCDAPOCD_MT000040_Performer1
   */
  function setPerformer($praticien) {
    $performer = new CCDAPOCD_MT000040_Performer1();
    $performer->setTypeCode("PRF");
    $performer->setAssignedEntity(self::$role->setAssignedEntity($praticien, true));
    return $performer;
  }
}