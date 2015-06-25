<?php

/**
 * $Id$
 *
 * @category Patients
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$praticien_id = CValue::post("praticien_id");
$all_prats    = CValue::post("all_prats");
$step         = (int)CValue::post("step");
$start        = (int)CValue::post("start");
$directory    = CValue::post("directory");
$ignore_files = CValue::post("ignore_files");
$generate_pdfpreviews = CValue::post("generate_pdfpreviews");

if (!$all_prats && !$praticien_id) {
  CAppUI::stepAjax("Veuillez choisir au moins un praticien, ou cocher 'Tous les praticiens'", UI_MSG_WARNING);
  return;
}

if (!is_dir($directory)) {
  CAppUI::stepAjax("'%s' is not a directory", UI_MSG_WARNING, $directory);
  return;
}

$directory = str_replace("\\\\", "\\", $directory);

CValue::setSession("praticien_id", $praticien_id);
CValue::setSession("all_prats", $all_prats);
CValue::setSession("step", $step);
CValue::setSession("start", $start);
CValue::setSession("directory", $directory);
CValue::setSession("ignore_files", $ignore_files);
CValue::setSession("generate_pdfpreviews", $generate_pdfpreviews);

$step = min($step, 1000);

CStoredObject::$useObjectCache = false;

$backrefs_tree = array(
  "CPatient" => array(
    "identifiants",
    "notes",
    "files",
    "documents",
    "permissions",
    "observation_result_sets",
    "constantes",
    "contextes_constante",
    "consultations",
    "correspondants",
    "correspondants_patient",
    "sejours",
    "dossier_medical",
    "correspondants_courrier",
    "grossesses",
    "allaitements",
    "patient_observation_result_sets",
    "patient_links",
    'arret_travail',
    "facture_patient_consult",
    "facture_patient_sejour",
  ),
  "CConsultation" => array(
    "files",
    "documents",
    "notes",
    "consult_anesth",
    "examaudio",
    "examcomp",
    "examnyha",
    "exampossum",
    "sejours_lies",
    "intervs_liees",
    "consults_liees",

    // Codable
    "facturable",
    "actes_ngap",
    "actes_ccam",
    "codages_ccam",
    "actes_caisse",
  ),
  "CConsultAnesth" => array(
    "files",
    "documents",
    "notes",
    "techniques",
  ),

  "CSejour" => array(
    "identifiants",
    "files",
    "documents",
    "notes",
    "dossier_medical",
    "operations",

    // Codable
    "facturable",
    "actes_ngap",
    "actes_ccam",
    "codages_ccam",
    "actes_caisse",
  ),
  "COperation" => array(
    "files",
    "documents",
    "notes",
    "anesth_perops",

    // Codable
    "facturable",
    "actes_ngap",
    "actes_ccam",
    "actes_caisse",
  ),
  "CCompteRendu" => array(
    "files",
  ),
  "CDossierMedical" => array(
    "antecedents",
    "traitements",
    "etats_dent",
  ),

  "CFactureCabinet" => array(
    "items",
    "reglements",
  ),

  "CFactureEtablissement" => array(
    "items",
    "reglements",
  ),
);

$fwdrefs_tree = array(
  "CPatient" => array(
    "medecin_traitant",
  ),
  "CConstantesMedicales" => array(
    "context_id",
    "patient_id",
    "user_id",
  ),
  "CConsultation" => array(
    "plageconsult_id",
    "sejour_id",
    "grossesse_id",
    "patient_id",
    "consult_related_id",
  ),
  "CConsultAnesth" => array(
    "consultation_id",
    "operation_id",
    "sejour_id",
    "chir_id",
  ),
  "CPlageconsult" => array(
    "chir_id",
  ),
  "CSejour" => array(
    "patient_id",
    "praticien_id",
    "service_id",
    "group_id",
    "grossesse_id",
  ),
  "COperation" => array(
    "sejour_id",
    "chir_id",
    "anesth_id",
    "plageop_id",
    "salle_id",
    "type_anesth",
    "consult_related_id",
    "prat_visite_anesth_id",
  ),
  "CGrossesse" => array(
    "group_id",
    "parturiente_id",
  ),
  "CCorrespondant" => array(
    "patient_id",
    "medecin_id",
  ),
  "CMediusers" => array(
    "user_id",
  ),
  "CPlageOp" => array(
    "chir_id",
    "anesth_id",
    "spec_id",
    "salle_id",
  ),

  // -- Actes
  "CActeCCAM" => array(
    "executant_id",
  ),
  "CActeNGAP" => array(
    "executant_id",
  ),
  "CActeCaisse" => array(
    "executant_id",
  ),
  "CFraidDivers" => array(
    "executant_id",
  ),
  // -- Fin Actes

  "CFactureItem" => array(
    "object_id",
  ),

  "CFactureLiaison" => array(
    "facture_id",
    "object_id",
  ),

  "CFactureCabinet" => array(
    "group_id",
    "patient_id",
    "praticien_id",
  ),

  "CFactureEtablissement" => array(
    "group_id",
    "patient_id",
    "praticien_id",
  ),

  "CTypeAnesth" => array(
    "group_id",
  ),

  "CFile" => array(
    "object_id",
    "author_id",
  ),

  "CCompteRendu" => array(
    "object_id",
    "author_id",

    "user_id",
    "function_id",
    "group_id",

    "content_id",

    "locker_id",
  ),
);

$patient = new CPatient();
$ds = $patient->getDS();

$order = array(
  "patients.nom",
  "patients.nom_jeune_fille",
  "patients.prenom",
  "patients.naissance",
  "patients.patient_id",
);

if ($all_prats) {
  $limit = "$start, $step";
  /** @var CPatient[] $patients */
  $patients = $patient->loadList(null, $order, $limit);

  $patient_count = count($patients);

  $patient_total = $patient->countList();
}
else {
  $ljoin_consult = array(
    "consultation" => "consultation.patient_id = patients.patient_id",
    "plageconsult" => "plageconsult.plageconsult_id = consultation.plageconsult_id",
  );

  $where_consult                         = array();
  $where_consult["plageconsult.chir_id"] = $ds->prepareIn($praticien_id);

  $patient_ids_consult = $patient->loadIds($where_consult, $order, null, "patients.patient_id", $ljoin_consult);

  $ljoin_sejour = array(
    "sejour" => "sejour.patient_id = patients.patient_id",
  );

  $where_sejour                        = array();
  $where_sejour["sejour.praticien_id"] = $ds->prepareIn($praticien_id);

  $patient_ids_sejour = $patient->loadIds($where_sejour, $order, null, "patients.patient_id", $ljoin_sejour);

  $patient_ids = array_merge($patient_ids_consult, $patient_ids_sejour);
  $patient_ids = array_unique($patient_ids);

  $patient_total = count($patient_ids);

  $patient_ids = array_slice($patient_ids, $start, $step);

  $patient_count = count($patients);

  $where = array(
    "patient_id" => $patient->getDS()->prepareIn($patient_ids),
  );

  /** @var CPatient[] $patients */
  $patients = $patient->loadList($where);
}

CAppUI::stepAjax("%d patients à exporter", UI_MSG_OK, $patient_total);

//$date = CMbDT::format(null, "%Y-%m-%d_%H-%M-%S");
$date = CMbDT::format(null, "%Y-%m-%d");

foreach ($patients as $_patient) {
  try {
    $dir = "$directory/export-$date/{$_patient->_guid}";

    if (is_dir($dir)) {
      continue;
    }

    CMbPath::forceDir($dir);

    $export = new CMbObjectExport($_patient, $backrefs_tree);

    $callback = function (CStoredObject $object, $node, $depth) use ($export, $dir, $ignore_files, $generate_pdfpreviews) {
      switch ($object->_class) {
        case "CCompteRendu":
          /** @var CCompteRendu $object */
          if ($generate_pdfpreviews) {
            $object->makePDFpreview(true);
          }
          break;

        case "CFile":
          if ($ignore_files) {
            break;
          }

          /** @var CFile $object */
          $_dir = "$dir/$object->object_class/$object->object_id";
          CMbPath::forceDir($_dir);

          file_put_contents($_dir."/".$object->file_real_filename, @$object->getBinaryContent());
          break;

        default:
          // Do nothing
      }
    };

    $export->empty_values = false;
    $export->setObjectCallback($callback);
    $export->setForwardRefsTree($fwdrefs_tree);

    $xml = $export->toDOM()->saveXML();
    file_put_contents("$dir/export.xml", $xml);

    //CMbPath::zip($dir, dirname($dir)."/export-$date.zip");
  }
  catch (CMbException $e) {
    $e->stepAjax(UI_MSG_ERROR);
  }
}

CAppUI::stepAjax("%d patients au total", UI_MSG_OK, $patient_count);

if ($patient_count) {
  CAppUI::js("nextStepPatients()");
}
