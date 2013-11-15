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
 * Permet de générer le CDA selon les champs générique
 */
class CCDAFactory {
  /** @var String */
  public $root;
  /** @var CCompteRendu */
  public $docItem;
  /** @var COperation|CConsultAnesth|CConsultation|CSejour */
  public $targetObject;
  /** @var CPatient */
  public $patient;
  /** @var CUser|CMediusers */
  public $practicien;
  /** @var CCDADomDocument */
  public $dom_cda;

  public $version;
  public $mediaType;
  public $file;
  public $nom;
  public $id_cda;
  public $id_cda_lot;
  public $realm_code;
  public $langage;
  public $confidentialite;
  public $date_creation;
  public $code;
  public $date_author;
  public $industry_code;
  public $healt_care;
  public $service_event = array();
  public $templateId = array();
  public $old_version;

  /**
   * construct
   *
   * @param CCompteRendu|CFile $docItem Document
   */
  function __construct($docItem) {
    $this->docItem = $docItem;
  }

  /**
   * Extraction des données pour alimenter le CDA
   *
   * @return void
   */
  function extractData() {
    $docItem = $this->docItem;
    $this->realm_code    = "FR";
    $this->langage       = $docItem->language;
    $docItem->loadLastLog();
    $this->date_creation = $docItem->_ref_last_log->date;
    $this->date_author = $docItem->_ref_last_log->date;
    $this->targetObject = $object = $docItem->loadTargetObject();
    if ($object instanceof CConsultAnesth) {
      $this->targetObject = $object = $object->loadRefConsultation();
    }
    $this->practicien = $object->loadRefPraticien();
    $this->patient    = $object->loadRefPatient();
    $this->patient->loadLastINS();
    $this->docItem    = $docItem;
    $this->root       = CMbOID::getOIDFromClass($docItem);

    $this->practicien->loadRefFunction();
    $this->practicien->loadRefOtherSpec();
    $group = new CGroups();
    $group->load($this->practicien->_group_id);
    $group->loadLastId400("cda_association_code");
    $this->healt_care = CCdaTools::loadEntryJV("CI-SIS_jdv_healthcareFacilityTypeCode.xml", $group->_ref_last_id400->id400);

    if ($docItem instanceof CFile) {
      $version = "1";
      $nom = $docItem->file_name;
      $nom = substr($nom, 0, strrpos($nom, "."));
    }
    else {
      $nom = $docItem->nom;
      $version = $docItem->version;
    }
    $this->nom = $nom;
    $this->version = $version;

    $this->id_cda_lot = $this->root.".".$docItem->_id;
    $this->id_cda = $this->id_cda_lot.".".$version;

    $confidentialite = "N";
    if ($docItem->private) {
      $confidentialite = "R";
    }
    $this->confidentialite = CCdaTools::loadEntryJV("CI-SIS_jdv_confidentialityCode.xml", $confidentialite);

    if ($docItem->type_doc) {
      $type = explode("^", $docItem->type_doc);
      $this->code = CCdaTools::loadEntryJV("CI-SIS_jdv_typeCode.xml", $type[1]);
    }

    //conformité HL7
    $this->templateId[] = $this->createTemplateID("2.16.840.1.113883.2.8.2.1", "HL7 France");
    //Conformité CI-SIS
    $this->templateId[] = $this->createTemplateID("1.2.250.1.213.1.1.1.1", "CI-SIS");
    //Confirmité IHE XSD-SD => contenu non structuré
    $this->templateId[] = $this->createTemplateID("1.3.6.1.4.1.19376.1.2.20", "IHE XDS-SD");

    $this->industry_code = CCdaTools::loadEntryJV("CI-SIS_jdv_practiceSettingCode.xml", "ETABLISSEMENT");

    $mediaType = "application/pdf";

    if ($docItem instanceof CFile) {
      $file = $docItem;
      switch ($docItem->file_type) {
        case "text/plain":
        case "image/jpeg":
        case "image/tiff":
        case "application/pdf":
          $mediaType = $docItem->file_type;
          break;
        case "image/jpg":
          $mediaType = "image/jpeg";
          break;
        case "application/rtf":
          $mediaType = "text/rtf";
          break;
        default:
          $docItem->convertToPDF();
          $file = $docItem->loadPDFconverted();
      }
    }
    else {
      $docItem->makePDFpreview(1, 0);
      $file = $docItem->_ref_file;
    }
    $this->file      = $file;
    $this->mediaType = $mediaType;
    $service["nullflavor"] = null;

    switch (get_class($object)) {
      case "CSejour":
        /** @var CSejour $object CSejour */

        $dp = $object->DP;
        $service["time_start"] = $object->entree;
        $service["time_stop"]  = $object->_sortie;
        $service["executant"] = $object->loadRefPraticien();
        if ($dp) {
          $service["oid"]       = "2.16.840.1.113883.6.3";
          $service["code"]      = $dp;
          $service["type_code"] = "cim10";
        }
        else {
          $service["nullflavor"] = "UNK";
        }
        break;
      case "COperation":
        /** @var COperation $object COperation */
        $object->loadRefsActesCCAM();
        $no_acte = 0;
        foreach ($object->_ref_actes_ccam as $acteCcam) {
          if ($acteCcam->code_activite === "4" || !$acteCcam->_check_coded  || $no_acte >= 1) {
            continue;
          }

          $service["time_start"] = $acteCcam->execution;
          $service["time_stop"]  = "";
          $service["code"]       = $acteCcam->code_acte;
          $service["oid"]        = "1.2.250.1.213.2.5";
          $acteCcam->loadRefExecutant();
          $service["executant"] = $acteCcam->_ref_executant;
          $service["type_code"] = "ccam";
          $no_acte++;
        }

        if ($no_acte === 0) {
          $service["time_start"] = $object->debut_op;
          $service["time_stop"]  = $object->fin_op;
          $service["executant"]  = $object->loadRefPraticien();
          $service["nullflavor"] = "UNK";
        }
        break;
      case "CConsultation":
        /** @var CConsultation $object CConsultation */
        $object->loadRefPlageConsult();

        $object->loadRefsActesCCAM();
        $no_acte = 0;
        foreach ($object->_ref_actes_ccam as $acteCcam) {
          if (!$acteCcam->_check_coded || $acteCcam->code_activite === "4" || $no_acte >= 1) {
            continue;
          }

          $service["time_start"] = $acteCcam->execution;
          $service["time_stop"]  = "";
          $service["code"]       = $acteCcam->code_acte;
          $service["oid"]        = "1.2.250.1.213.2.5";
          $acteCcam->loadRefExecutant();
          $service["executant"] = $acteCcam->_ref_executant;
          $service["type_code"] = "ccam";
          $no_acte++;
        }

        if ($no_acte === 0) {
          $service["time_start"] = $object->_datetime;
          $service["time_stop"]  = $object->_date_fin;
          $service["executant"]  = $object->loadRefPraticien();
          $service["nullflavor"] = "UNK";
        }
        break;
    }

    $this->service_event = $service;

    if ($this->old_version) {
      $oid = CMbOID::getOIDFromClass($docItem);
      $this->old_version = "$oid.$this->old_version";
    }
  }

  /**
   * Generation du CDA
   *
   * @return string
   */
  function generateCDA() {
    $this->extractData();
    $document_cda = new CCDADocumentCDA();
    $cda = $document_cda->generateCDA($this);
    $xml = $cda->toXML("ClinicalDocument", "urn:hl7-org:v3");
    $xml->purgeEmptyElements();
    $this->dom_cda = $xml;
    return $xml->saveXML($xml->documentElement);
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
}