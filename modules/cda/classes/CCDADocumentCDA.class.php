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
 * Regroupe les fonctions pour créer un document CDA
 */
class CCDADocumentCDA extends CCDAClasseCda{

  /** @var String */
  static $root;

  /** @var CCompteRendu */
  static $docItem;

  /** @var CPatient */
  static $patient;

  /** @var CCDAEntiteCDA */
  static $entite;

  /** @var CCDARoleCDA */
  static $role;

  /** @var CCDAParticipationCDA */
  static $participation;

  /** @var CCDAActRelationshipCDA */
  static $actRelationship;

  /** @var CCDAActCDA */
  static $act;

  /**
   * Création du CDA
   *
   * @param CCompteRendu $docItem CCompteRendu
   *
   * @return CCDAPOCD_MT000040_ClinicalDocument
   */
  function generateCDA(CCompteRendu $docItem) {
    $docItem->loadLastLog();
    $docItem->loadTargetObject();
    self::$participation   = new CCDAParticipationCDA();
    self::$entite          = new CCDAEntiteCDA();
    self::$act             = new CCDAActCDA();
    self::$actRelationship = new CCDAActRelationshipCDA();
    self::$role            = new CCDARoleCDA();
    self::$docItem         = $docItem;
    self::$root            = CAppUI::conf("cda OID_root");
    $this->getPatientFromDoc(self::$docItem);

    $act = new CCDAActCDA();
    $CDA = $act->setClinicalDocument();

    return $CDA;
  }

  /**
   * Création de templateId
   *
   * @param String $root      String
   * @param String $extension null
   *
   * @return CCDAII
   */
  function createTemplateID($root, $extension = null) {
    $ii = new CCDAII();
    $ii->setRoot($root);
    $ii->setExtension($extension);
    return $ii;
  }

  /**
   * Transforme une chaine date au format time CDA
   *
   * @param String $date      String
   * @param bool   $naissance false
   *
   * @return string
   */
  function getTimeToUtc($date, $naissance = false) {
    $timezone = new DateTimeZone(CAppUI::conf("timezone"));
    $date = new DateTime($date, $timezone);
    if ($naissance) {
      return $date->format("Ymd");
    }
    return $date->format("YmdHisO");
  }

  /**
   * Création de l'adresse de la personne passé en paramètre
   *
   * @param CPerson $user CPerson
   *
   * @return CCDAAD
   */
  function setAddress($user) {
    $userCity =  $user->_pcountry;
    $userPostalCode = $user->_ppostalCode;
    $userStreetAddress = $user->_pstreetAddress;

    $ad = new CCDAAD();
    if (!$userCity && !$userPostalCode && !$userStreetAddress) {
      $ad->setNullFlavor("NASK");
      return $ad;
    }

    $street = new CCDA_adxp_streetAddressLine();
    $street->setData($userStreetAddress);
    $street2 = new CCDA_adxp_streetAddressLine();
    $street2->setData($userPostalCode." ".$userCity);

    $ad->append("streetAddressLine", $street);
    $ad->append("streetAddressLine", $street2);

    return $ad;
  }

  /**
   * Récupère le patient du document
   *
   * @param CCompteRendu $docItem CCompteRendu
   *
   * @return CPatient
   */
  function getPatientFromDoc(CCompteRendu $docItem) {
    $object = $docItem->_ref_object;
    if ($object instanceof CPatient) {
      return self::$patient = $object;
    }
    return self::$patient = $object->loadRefPatient();
  }

  /**
   * Ajoute les téléphone de la personne dans l'objet qui a appelé cette méthode
   *
   * @param Object  $object  Object
   * @param CPerson $patient CPerson
   *
   * @return void
   */
  function setTelecom($object, $patient) {
    $patientPhoneNumber = $patient->_pphoneNumber;
    $patientMobilePhoneNumber = $patient->_pmobilePhoneNumber;
    $patientEmail = $patient->_pemail;

    $tel = new CCDATEL();
    if (!$patientPhoneNumber && !$patientMobilePhoneNumber && !$patientEmail) {
      $tel->setNullFlavor("NASK");
      $object->appendTelecom($tel);
      return;
    }

    $tel->setValue($patientPhoneNumber ? "tel:".$patientPhoneNumber : "");
    $object->appendTelecom($tel);

    $tel = new CCDATEL();
    $tel->setValue($patientMobilePhoneNumber ? "tel:".$patientMobilePhoneNumber : "");
    $object->appendTelecom($tel);

    $tel = new CCDATEL();
    $tel->setValue($patientEmail ? "mailto:".$patientEmail : "");
    $object->appendTelecom($tel);
  }

  /**
   * Retourne le code associé à la situation familiale
   *
   * @param String $status String
   *
   * @return CCDACE
   */
  function getMaritalStatus($status) {
    $ce = new CCDACE();
    $ce->setCodeSystem("1.3.6.1.4.1.21367.100.1");
    switch ($status) {
      case "S":
        $ce->setCode("S");
        $ce->setDisplayName("Célibataire");
        break;
      case "M":
        $ce->setCode("M");
        $ce->setDisplayName("Marié");
        break;
      case "G":
        $ce->setCode("G");
        $ce->setDisplayName("Concubin");
        break;
      case "D":
        $ce->setCode("D");
        $ce->setDisplayName("Divorcé");
        break;
      case "W":
        $ce->setCode("W");
        $ce->setDisplayName("Veuf/Veuve");
        break;
      case "A":
        $ce->setCode("A");
        $ce->setDisplayName("Séparé");
        break;
      case "P":
        $ce->setCode("P");
        $ce->setDisplayName("Pacte civil de solidarité (PACS)");
        break;
      default:
        $ce->setCode("U");
        $ce->setDisplayName("Inconnu");
    }
    return $ce;
  }

  /**
   * Retourne le code associé au sexe de la personne
   *
   * @param String $sexe String
   *
   * @return CCDACE
   */
  function getAdministrativeGenderCode($sexe) {
    $ce = new CCDACE();
    $ce->setCode(mb_strtoupper($sexe));
    $ce->setCodeSystem("2.16.840.1.113883.5.1");
    switch ($sexe) {
      case "f":
        $ce->setDisplayName("Féminin");
        break;
      case "m":
        $ce->setDisplayName("Masculin");
        break;
      default:
        $ce->setCode("U");
        $ce->setDisplayName("Inconnu");
    }
    return $ce;
  }

  /**
   * Attribution de l'id au PS
   *
   * @param Object  $assigned Object
   * @param IPerson $user     IPerson
   *
   * @return void
   */
  function setIdPs($assigned, $user) {

    if (!$user->adeli && !$user->rpps) {
      return;
    }

    if ($user->adeli) {
      $ii = new CCDAII();
      $ii->setRoot("1.2.250.1.71.4.2.1");
      $ii->setAssigningAuthorityName("GIP-CPS");
      $ii->setExtension("0$user->adeli");
      $assigned->appendId($ii);
    }

    if ($user->rpps) {
      $ii = new CCDAII();
      $ii->setRoot("1.2.250.1.71.4.2.1");
      $ii->setAssigningAuthorityName("GIP-CPS");
      $ii->setExtension("8$user->rpps");
      $assigned->appendId($ii);
    }
  }


  /**
   * Affectation id à l'établissement
   *
   * @param Object  $entite Object
   * @param CGroups $etab   CGroups
   */
  function setIdEtablissement($entite, $etab) {

    if ($etab->siret) {
      $ii = new CCDAII();
      $ii->setRoot("1.2.250.1.71.4.2.2");
      $ii->setExtension("3".$etab->siret);
      $ii->setAssigningAuthorityName("GIP-CPS");
      $entite->appendId($ii);
    }

    if ($etab->finess) {
      $ii = new CCDAII();
      $ii->setRoot("1.2.250.1.71.4.2.2");
      $ii->setExtension("1".$etab->finess);
      $ii->setAssigningAuthorityName("GIP-CPS");
      $entite->appendId($ii);
    }
  }

  /**
   * Affectation des documents
   *
   * @param CCDAPOCD_MT000040_ClinicalDocument $clinicalDoc CCDAPOCD_MT000040_ClinicalDocument
   *
   * @return void
   */
  function setDocumentationOF($clinicalDoc) {
    $docItem = self::$docItem;
    $object = $docItem->_ref_object;
    $object->loadRefPraticien();

    switch (get_class($object)) {
      case "CSejour":
        $documentationOf = new CCDAPOCD_MT000040_DocumentationOf();
        $serviceEvent = new CCDAPOCD_MT000040_ServiceEvent();
        $low = $object->entree_reelle;
        if (!$object->entree_reelle) {
          $low = $object->entree_prevue;
        }
        $ivlTs = $this->createIvlTs($low, $object->sortie_reelle);
        $serviceEvent->setEffectiveTime($ivlTs);

        //@todo: faire le diagnostic principal et secondaire, plus code CCAM, cim10(s'il y a).
        $object->DP;

        $performer = new CCDAPOCD_MT000040_Performer1();
        $performer->setTypeCode("PRF");
        $performer->setAssignedEntity(self::$role->setAssignedEntity($object->_ref_praticien, true));
        $serviceEvent->appendPerformer($performer);
        $documentationOf->setServiceEvent($serviceEvent);
        $clinicalDoc->appendDocumentationOf($documentationOf);
        break;
      case "COperation":
        $object->loadRefsActesCCAM();
        foreach ($object->_ref_actes_ccam as $acteCcam) {
          if (!$acteCcam->_check_coded) {
            continue;
          }
          $acteCcam->loadRefExecutant();
          $documentationOf = new CCDAPOCD_MT000040_DocumentationOf();
          $serviceEvent = new CCDAPOCD_MT000040_ServiceEvent();
          $ii = new CCDAII();
          $ii->setRoot("1.2.250.1.213.2.5");
          $ii->setExtension($acteCcam->code_acte);
          $serviceEvent->appendId($ii);
          $ivl = $this->createIvlTs($acteCcam->execution, "");
          $serviceEvent->setEffectiveTime($ivl);

          $performer = new CCDAPOCD_MT000040_Performer1();
          $performer->setTypeCode("PRF");
          $performer->setAssignedEntity(self::$role->setAssignedEntity($acteCcam->_ref_executant));
          $serviceEvent->appendPerformer($performer);
          $documentationOf->setServiceEvent($serviceEvent);

          $clinicalDoc->appendDocumentationOf($documentationOf);
        }

        $object->loadRefSejour();

        //@todo: faire sejour

        break;
      case "CConsultation":

        break;
    }

  }

  /**
   * Création d'un ivl_ts avec une valeur basse et haute
   *
   * @param String $low  String
   * @param String $high String
   *
   * @return CCDAIVL_TS
   */
  function createIvlTs($low, $high, $nullFlavor = false) {
    $ivlTs = new CCDAIVL_TS();
    if ($nullFlavor && !$low && !$high) {
      $ivlTs->setNullFlavor("UNK");
      return $ivlTs;
    }

    $low = $this->getTimeToUtc($low);
    $high = $this->getTimeToUtc($high);

    $ivxbL = new CCDAIVXB_TS();
    $ivxbL->setValue($low);
    $ivlTs->setLow($ivxbL);
    $ivxbH = new CCDAIVXB_TS();
    $ivxbH->setValue($high);
    $ivlTs->setHigh($ivxbH);

    return $ivlTs;
  }
}