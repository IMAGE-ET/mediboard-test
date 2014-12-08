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
 * Classe pour les Document Item
 */
class CCDAFactoryDocItem extends CCDAFactory {

  /**
   * @see parent::extractData
   */
  function extractData() {
    /** @var CDocumentItem $docItem */
    $docItem             = $this->mbObject;
    $this->realm_code    = "FR";
    $this->langage       = $docItem->language;
    //Récupération du dernier log qui correspond à la date de création de cette version
    $last_log = $docItem->loadLastLog();
    $this->date_creation = $last_log->date;
    $this->date_author   = $last_log->date;
    $this->targetObject  = $object = $docItem->loadTargetObject();
    if ($object instanceof CConsultAnesth) {
      $this->targetObject = $object = $object->loadRefConsultation();
    }
    $this->practicien = $object->loadRefPraticien();
    $this->patient    = $object->loadRefPatient();
    $this->patient->loadLastINS();
    $this->patient->loadIPP();
    $this->mbObject   = $docItem;
    $this->root       = CMbOID::getOIDFromClass($docItem, $this->receiver);

    $this->practicien->loadRefFunction();
    $this->practicien->loadRefOtherSpec();
    $group = new CGroups();
    $group->load($this->practicien->_group_id);
    $this->healt_care = CCdaTools::loadEntryJV("CI-SIS_jdv_healthcareFacilityTypeCode.xml", CIdSante400::getValueFor($group, "cda_association_code"));

    if ($docItem instanceof CFile) {
      $this->version = "1";
      $this->nom     = basename($docItem->file_name);
    }
    else {
      $this->version = $docItem->version;
      $this->nom     = $docItem->nom;
    }

    $this->id_cda_lot = "$this->root.$docItem->_id";
    $this->id_cda     = "$this->id_cda_lot.$this->version";

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

    //Génération du PDF
    if ($docItem instanceof CFile) {
      $path = $docItem->_file_path;
      switch ($docItem->file_type) {
        case "image/tiff":
          $mediaType = "image/tiff";
          break;
        case "application/pdf":
          $mediaType = $docItem->file_type;
          $path = CCdaTools::generatePDFA($docItem->_file_path);
          break;
        case "image/jpeg":
        case "image/jpg":
          $mediaType = "image/jpeg";
          break;
        case "application/rtf":
          $mediaType = "text/rtf";
          break;
        default:
          $docItem->convertToPDF();
          $file = $docItem->loadPDFconverted();
          $path = CCdaTools::generatePDFA($file->_file_path);
      }
    }
    else {
      if ($msg = $docItem->makePDFpreview(1, 0)) {
        throw new CMbException($msg);
      }
      $file = $docItem->_ref_file;
      $path = CCdaTools::generatePDFA($file->_file_path);
    }
    $this->file      = $path;
    $this->mediaType = $mediaType;
    $service["nullflavor"] = null;

    switch (get_class($object)) {
      case "CSejour":
        /** @var CSejour $object CSejour */

        $dp = $object->DP;
        $service["time_start"] = $object->entree;
        $service["time_stop"]  = $object->sortie;
        $service["executant"]  = $object->loadRefPraticien();
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
        $no_acte = 0;
        foreach ($object->loadRefsActesCCAM() as $_acte_ccam) {
          if ($_acte_ccam->code_activite === "4" || !$_acte_ccam->_check_coded  || $no_acte >= 1) {
            continue;
          }

          $service["time_start"] = $_acte_ccam->execution;
          $service["time_stop"]  = "";
          $service["code"]       = $_acte_ccam->code_acte;
          $service["oid"]        = "1.2.250.1.213.2.5";
          $_acte_ccam->loadRefExecutant();
          $service["executant"] = $_acte_ccam->_ref_executant;
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


        $no_acte = 0;
        foreach ($object->loadRefsActesCCAM() as $_acte_ccam) {
          if (!$_acte_ccam->_check_coded || $_acte_ccam->code_activite === "4" || $no_acte >= 1) {
            continue;
          }

          $service["time_start"] = $_acte_ccam->execution;
          $service["time_stop"]  = "";
          $service["code"]       = $_acte_ccam->code_acte;
          $service["oid"]        = "1.2.250.1.213.2.5";
          $_acte_ccam->loadRefExecutant();
          $service["executant"] = $_acte_ccam->_ref_executant;
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
      default:
    }

    $this->service_event = $service;

    if ($this->old_version) {
      $oid = CMbOID::getOIDFromClass($docItem, $this->receiver);
      $this->old_version = "$oid.$this->old_id.$this->old_version";
    }
  }
}
