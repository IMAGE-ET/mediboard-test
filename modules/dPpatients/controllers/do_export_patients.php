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
$step         = CValue::post("step");
$start        = CValue::post("start");
$directory    = CValue::post("directory");

if (!$praticien_id) {
  CAppUI::stepAjax("Veuillez choisir un praticien", UI_MSG_WARNING);
  return;
}

if (!is_dir($directory)) {
  CAppUI::stepAjax("'%s' is not a directory", UI_MSG_WARNING, $directory);
  return;
}

$directory = str_replace("\\\\", "\\", $directory);

CValue::setSession("praticien_id", $praticien_id);
CValue::setSession("step", $step);
CValue::setSession("start", $start);
CValue::setSession("directory", $directory);

$step = min($step, 1000);

CStoredObject::$useObjectCache = false;

$backrefs_tree = array(
  "CPatient" => array(
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
  ),
  "CConsultation" => array(
    "files",
    "documents",
    "notes",
  ),
  "CSejour" => array(
    "files",
    "documents",
    "notes",
  ),
  "CCompteRendu" => array(
    "files",
  ),
  "CDossierMedical" => array(
    "antecedents",
    "traitements",
    "etats_dent",
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
  ),
  "CPlageconsult" => array(
    "chir_id",
  ),
  "CSejour" => array(
    "praticien_id",
    "group_id",
    "grossesse_id",
  ),
  "CGrossesse" => array(
    "group_id",
    "parturiente_id",
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

$ljoin_consult = array(
  "consultation" => "consultation.patient_id = patients.patient_id",
  "plageconsult" => "plageconsult.plageconsult_id = consultation.plageconsult_id",
);

$where_consult = array(
  "plageconsult.chir_id" => $ds->prepareIn($praticien_id),
);

$patient_ids_consult = $patient->loadIds($where_consult, $order, null, "patients.patient_id", $ljoin_consult);

$ljoin_sejour = array(
  "sejour"      => "sejour.patient_id = patients.patient_id",
);

$where_sejour = array(
  "sejour.praticien_id" => $ds->prepareIn($praticien_id),
);

$patient_ids_sejour  = $patient->loadIds($where_sejour,  $order, null, "patients.patient_id", $ljoin_sejour);

$patient_ids = array_merge($patient_ids_consult, $patient_ids_sejour);
$patient_ids = array_unique($patient_ids);

CAppUI::stepAjax("%d patients à exporter", UI_MSG_OK, count($patient_ids));

$patient_ids = array_slice($patient_ids, $start, $step);

$where = array(
  "patient_id" => $patient->getDS()->prepareIn($patient_ids),
);

/** @var CPatient[] $patients */
$patients = $patient->loadList($where);
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

    $callback = function (CStoredObject $object, $node, $depth) use ($export, $dir) {
      switch ($object->_class) {
        case "CCompteRendu":
          /** @var CCompteRendu $object */
          $object->makePDFpreview(true);
          break;

        case "CFile":
          /** @var CFile $object */
          $_dir = "$dir/$object->object_class/$object->object_id";
          CMbPath::forceDir($_dir);

          file_put_contents($_dir."/".$object->file_real_filename, @$object->getBinaryContent());
          break;

        default:
          // Do nothing
      }
    };

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

CAppUI::stepAjax("%d patients exportés", UI_MSG_OK, count($patient_ids));

if (count($patient_ids)) {
  CAppUI::js("nextStep()");
}

