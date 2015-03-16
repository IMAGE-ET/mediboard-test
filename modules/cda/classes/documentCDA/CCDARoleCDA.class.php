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
 * Classe regroupant les fonctions de type Role
 */
class CCDARoleCDA extends CCDADocumentCDA {

  /**
   * Affectation champ pour les fonction de type assigned
   *
   * @param CCDAPOCD_MT000040_AssignedAuthor $assigned CCDAPOCD_MT000040_AssignedAuthor
   * @param CPerson                          $mediUser Cperson
   *
   * @return void
   */
  function setAssigned($assigned, $mediUser) {
    if ($mediUser instanceof CUser) {
      $mediUser = $mediUser->loadRefMediuser();
    }
    $mediUser->loadRefFunction();

    $this->setIdPs($assigned, $mediUser);

    $this->setTelecom($assigned, $mediUser);

    $ad = $this->setAddress($mediUser);
    $assigned->appendAddr($ad);
  }

  /**
   * Création du role de l'auteur
   *
   * @return CCDAPOCD_MT000040_AssignedAuthor
   */
  function setAssignedAuthor() {
    $assigned = new CCDAPOCD_MT000040_AssignedAuthor();

    $praticien = self::$cda_factory->practicien;
    $spec = $praticien->_ref_other_spec;
    $this->setAssigned($assigned, $praticien);
    if ($spec->libelle) {
      $ce = new CCDACE();
      $ce->setCode($spec->code);
      $ce->setDisplayName($spec->libelle);
      $ce->setCodeSystem($spec->oid);
      $assigned->setCode($ce);
    }

    $assigned->setAssignedPerson(parent::$entite->setPerson($praticien));
    $assigned->setRepresentedOrganization(parent::$entite->setOrganization($praticien));
    return $assigned;
  }

  /**
   * Création d'un assignedCustodian
   *
   * @return CCDAPOCD_MT000040_AssignedCustodian
   */
  function setAssignedCustodian() {
    $assignedCustodian = new CCDAPOCD_MT000040_AssignedCustodian();
    $assignedCustodian->setRepresentedCustodianOrganization(parent::$entite->setCustodianOrganization());
    return $assignedCustodian;
  }

  /**
   * Création du patientRole
   *
   * @return CCDAPOCD_MT000040_PatientRole
   */
  function setPatientRole() {
    $patientRole = new CCDAPOCD_MT000040_PatientRole();
    $patient = self::$cda_factory->patient;

    if ($patient->_ref_last_ins) {
      $ii = new CCDAII();
      $ii->setRoot("1.2.250.1.213.1.4.2");
      $ii->setExtension($patient->_ref_last_ins->ins);
      $patientRole->appendId($ii);
    }

    $ii = new CCDAII();
    $ii->setRoot(CMbOID::getOIDOfInstance($patient, self::$cda_factory->receiver));
    $ii->setExtension($patient->_id);
    $patientRole->appendId($ii);

    if ($patient->_IPP) {
      $ii = new CCDAII();
      /* @todo Gérer le master domaine*/
      //$group_domain = new CGroupDomain();
      //$group_domain->loadM
      $ii->setRoot(self::$cda_factory->root);
      $ii->setExtension($patient->_IPP);
      //libelle du domaine
      $ii->setAssigningAuthorityName("");
      $patientRole->appendId($ii);
    }

    $ad = $this->setAddress($patient);
    $patientRole->appendAddr($ad);

    $this->setTelecom($patientRole, $patient);

    $patientRole->setPatient(parent::$entite->setPatient());

    return $patientRole;
  }

  /**
   * Création du role lieu de naissance
   *
   * @return CCDAPOCD_MT000040_Birthplace
   */
  function setBirthPlace() {
    $birthplace = new CCDAPOCD_MT000040_Birthplace();
    $birthplace->setPlace(parent::$entite->setPlace());
    return $birthplace;
  }

  /**
   * Création de l'assignedEntity
   *
   * @param CUser|CMediUsers $user         CUser|CMediUsers
   * @param boolean          $organization false
   *
   * @return CCDAPOCD_MT000040_AssignedEntity
   */
  function setAssignedEntity($user, $organization = false) {
    $assignedEntity = new CCDAPOCD_MT000040_AssignedEntity();

    $this->setAssigned($assignedEntity, $user);

    if ($organization) {
      $assignedEntity->setRepresentedOrganization(parent::$entite->setOrganization($user));
    }

    $assignedEntity->setAssignedPerson(parent::$entite->setPerson($user));
    return $assignedEntity;
  }

  /**
   * Retourne un HealthCareFacility
   *
   * @return CCDAPOCD_MT000040_HealthCareFacility
   */
  function setHealthCareFacility() {
    $healt = new CCDAPOCD_MT000040_HealthCareFacility();
    $valeur = self::$cda_factory->healt_care;
    $ce = new CCDACE();
    $ce->setCode($valeur["code"]);
    $ce->setCodeSystem($valeur["codeSystem"]);
    $ce->setDisplayName($valeur["displayName"]);
    $healt->setCode($ce);

    return $healt;
  }
}