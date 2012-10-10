<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPpatients
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$where = array();
$ljoin = array();

$user_id = CValue::get("user_id");
$type_prescription = CValue::get("type_prescription");
CValue::setSession("produit", CValue::get("produit"));
CValue::setSession("user_id", $user_id);
CValue::setSession("type_prescription", $type_prescription);
$start = intval(CValue::get("start", 0));

$patient = new CPatient;
$ds = $patient->_spec->ds;

// fields
$fields = array(
  "CPatient" => array(
    "sexe" => "=", 
    "_age_min" => null, 
    "_age_max" => null, 
    "medecin_traitant" => null,
  ),
  "CAntecedent" => array(
    "rques" => "LIKE", 
  ),
  "CTraitement" => array(
    "traitement" => "LIKE", 
  ),
  "CConsultation" => array(
    "motif" => "LIKE", 
    "_rques_consult" => null,
    "_examen_consult" => null,
    //"_traitement_consult" => null,
    "conclusion" => "LIKE",
  ),
  "CSejour" => array(
    "type" => "=", 
    "convalescence" => "LIKE", 
    "_rques_sejour" => null,
    "entree" => null,
    "sortie" => null,
    "libelle" => "LIKE",
  ),
  "COperation" => array(
    "materiel" => "LIKE", 
    "examen" => "LIKE", 
    //"rques" => "LIKE",
    "_libelle_interv" => null,
    "codes_ccam" => null,
    "_rques_interv" => null,
  ),
  "CPrescriptionLineMedicament" => array(
    "code_cis" => "=",
    "code_ucd" => "="
  )
);

$one_field = false;

foreach($fields as $_class => $_fields) {
  $data[$_class] = array_intersect_key($_GET, $_fields);
  $object = new $_class;
  $prefix = $object->_spec->table;
  
  foreach($data[$_class] as $_field => $_value) {
    CValue::setSession($_field, $_value);
    
    if ( $_value !== "" ) {
      $one_field = true;
    }
    
    if ( $_value === "" || !$_fields[$_field]) continue;
    
    if ($_fields[$_field] == "=")
      $where["$prefix.$_field"] = $ds->prepare(" = % ", $_value);
    else
      $where["$prefix.$_field"] = $ds->prepareLike("%$_value%");
  }
}

$sejour_data = $data["CSejour"];
if (!empty($sejour_data["entree"]) || !empty($sejour_data["sortie"])) {
  if (!empty($sejour_data["entree"])) {
    $where[] = "sejour.entree >=  '{$sejour_data['entree']}'";
    $where[] = "operations.date  >= '{$sejour_data['entree']}' OR 
                plagesop.date >= '{$sejour_data['entree']}'";
  }
  
  if (!empty($sejour_data["sortie"])) {
    $where[] = "sejour.entree <  '{$sejour_data['sortie']}'";
    $where[] = "operations.date  < '{$sejour_data['sortie']}' OR 
                plagesop.date < '{$sejour_data['sortie']}'";
  }
}

// CPatient ---------------------------
if (!empty($data["CPatient"]["_age_min"])) {
  $where[] = "DATEDIFF(sejour.entree_reelle, patients.naissance)/365 > {$data['CPatient']['_age_min']} OR ".
    "DATEDIFF(CONCAT(plageconsult.date, ' ', consultation.heure), patients.naissance)/365 > {$data['CPatient']['_age_min']}";
  //$where[] = "patients.naissance < '".mbDate("-".$data["CPatient"]["_age_min"]. "YEARS")."'";
}
if (!empty($data["CPatient"]["_age_max"])) {
  $where[] = "DATEDIFF(sejour.entree_reelle, patients.naissance)/365 <= {$data['CPatient']['_age_max']} OR ".
  "DATEDIFF(CONCAT(plageconsult.date, ' ', consultation.heure), patients.naissance)/365 <= {$data['CPatient']['_age_max']}";
  //$where[] = "patients.naissance > '".mbDate("-".$data["CPatient"]["_age_max"]. "YEARS")."'";
}
if (!empty($data["CPatient"]["medecin_traitant"])) {
  $one_field = true;
  $medecin_traitant_id = $data["CPatient"]["medecin_traitant"];
  if (CValue::get("only_medecin_traitant")) {
    $where[] = "patients.medecin_traitant = '$medecin_traitant_id'";
  }
  else {
    $where[] = "patients.medecin_traitant = '$medecin_traitant_id' OR 
                correspondant.medecin_id = '$medecin_traitant_id'";
    $ljoin["correspondant"] = "patients.patient_id = correspondant.patient_id";
  }
}

// CAntecedent ---------------------------
$dm_data = $data["CAntecedent"];
$where[] = "
  dossier_medical.object_class = 'CPatient' OR
  dossier_medical.dossier_medical_id IS NULL
";


// CConsultation ---------------------------
$consult_data = $data["CConsultation"];
if (!empty($consult_data["_rques_consult"])) {
  $where["consultation.rques"] = $ds->prepareLike("%{$consult_data['_rques_consult']}%");
}
if (!empty($consult_data["_examen_consult"])) {
  $where["consultation.examen"] = $ds->prepareLike("%{$consult_data['_examen_consult']}%");
}


// CSejour ----------------------------
if (!empty($sejour_data["_rques_sejour"])) {
  $where["sejour.rques"] = $ds->prepareLike("%{$sejour_data['_rques_sejour']}%");
}

// COperations ---------------------------
$interv_data = $data["COperation"];
if (!empty($interv_data["codes_ccam"])) {
  $codes = preg_split("/[\s,]+/", $interv_data["codes_ccam"]);
  
  $where_code = array();
  foreach($codes as $_code) {
    $where_code[] = "operations.codes_ccam ".$ds->prepareLike("%$_code%");
  }
  
  $where[] = implode(" AND ", $where_code);
}
if (!empty($interv_data["_rques_interv"])) {
  $where["operations.rques"] = $ds->prepareLike("%{$interv_data['_rques_interv']}%");
}
if (!empty($interv_data["_libelle_interv"])) {
  $where["operations.libelle"] = $ds->prepareLike("%{$interv_data['_libelle_interv']}%");
}
$where[] = "operations.chir_id = '$user_id' OR operations.chir_id IS NULL";
$where[] = "operations.annulee = '0' OR operations.annulee IS NULL";

$ljoin["consultation"] = "consultation.patient_id = patients.patient_id";
$ljoin["sejour"] = "sejour.patient_id = patients.patient_id";
$ljoin["dossier_medical"] = "dossier_medical.object_id = patients.patient_id";
$ljoin["antecedent"] = "antecedent.dossier_medical_id = dossier_medical.dossier_medical_id";
$ljoin["traitement"] = "traitement.dossier_medical_id = dossier_medical.dossier_medical_id";
$ljoin["operations"] = "operations.sejour_id = sejour.sejour_id";
$ljoin["plagesop"] = "plagesop.plageop_id = operations.plageop_id";
$ljoin["plageconsult"] = "plageconsult.plageconsult_id = consultation.plageconsult_id";

$list_patient = array();
$count_patient = array();

// CPrescription ----------------------------
$prescription_data = $data["CPrescriptionLineMedicament"];

if (!empty($prescription_data["code_cis"]) || !empty($prescription_data["code_ucd"])) {
  switch ($type_prescription) {
    case "externe":
      $ljoin["prescription"] = "prescription.object_class = 'CConsultation' AND prescription.object_id = consultation.consultation_id";
      break;
    case "pre_admission":
    case "sejour":
    case "sortie":
      $ljoin["prescription"] = "prescription.object_class = 'CSejour' AND prescription.object_id = sejour.sejour_id";
  }
  $where["prescription.type"] = "= '$type_prescription'";
  $ljoin["prescription_line_medicament"] = "prescription_line_medicament.prescription_id = prescription.prescription_id";
}

if ($one_field) {
  $list_patient = $patient->loadList($where, "patients.nom, patients.prenom", "$start,30", "patients.patient_id", $ljoin);
  $count_patient = count($patient->countMultipleList($where, null, "patients.patient_id", $ljoin));
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("one_field", $one_field);
$smarty->assign("start", $start);
$smarty->assign("user_id", $user_id);
$smarty->assign("list_patient", $list_patient);
$smarty->assign("count_patient", $count_patient);
$smarty->display("inc_recherche_dossier_clinique_results.tpl");
