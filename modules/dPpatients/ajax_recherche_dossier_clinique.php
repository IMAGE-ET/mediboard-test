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

$patient = new CPatient;
$ds = $patient->_spec->ds;

// fields
$fields = array(
  "CPatient" => array(
    "sexe" => "=", 
    "_age_min" => null, 
    "_age_max" => null, 
    "medecin_traitant" => "=",
  ),
  "CDossierMedical" => array(
    "codes_cim" => null, 
  ),
  "CSejour" => array(
    "type" => "=", 
    "convalescence" => "LIKE", 
    "rques" => "LIKE",
    "entree_reelle" => null,
    "sortie_reelle" => null,
  ),
  "COperation" => array(
    "materiel" => "LIKE", 
    "examen" => "LIKE", 
    //"rques" => "LIKE",
    "libelle" => "LIKE",
    "codes_ccam" => "LIKE",
  ),
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

// CPatient ---------------------------
if (!empty($data["CPatient"]["_age_min"])) {
  $where[] = "DATEDIFF(sejour.entree_reelle, patients.naissance)/365 > {$data['CPatient']['_age_min']}";
  //$where[] = "patients.naissance < '".mbDate("-".$data["CPatient"]["_age_min"]. "YEARS")."'";
}
if (!empty($data["CPatient"]["_age_max"])) {
  $where[] = "DATEDIFF(sejour.entree_reelle, patients.naissance)/365 <= {$data['CPatient']['_age_max']}";
  //$where[] = "patients.naissance > '".mbDate("-".$data["CPatient"]["_age_max"]. "YEARS")."'";
}

// CDossierMedical ---------------------------
$dm_data = $data["CDossierMedical"];
if (!empty($dm_data["codes_cim"])) {
  $codes = preg_split("/[\s,]+/", $dm_data["codes_cim"]);
  
  $where_code = array();
  foreach($codes as $_code) {
    $where_code[] = "dossier_medical.codes_cim ".$ds->prepareLike("%$_code%");
  }
  
  $where[] = implode(" AND ", $where_code);
}
$where[] = "
  dossier_medical.object_class = 'CPatient' OR
  dossier_medical.dossier_medical_id IS NULL
";

// CSejour ----------------------------
$sejour_data = $data["CSejour"];
if (!empty($sejour_data["entree_reelle"]) || !empty($sejour_data["sortie_reelle"])) {
  if (!empty($sejour_data["entree_reelle"])) {
    $where[] = "
      sejour.entree_reelle >  '{$sejour_data['entree_reelle']}' OR
      sejour.entree_reelle <= '{$sejour_data['entree_reelle']}' AND sejour.sortie_reelle > '{$sejour_data['entree_reelle']}'
    ";
  }
  
  if (!empty($sejour_data["sortie_reelle"])) {
    $where[] = "
      sejour.sortie_reelle <  '{$sejour_data['sortie_reelle']}' OR
      sejour.sortie_reelle >= '{$sejour_data['sortie_reelle']}' AND sejour.entree_reelle < '{$sejour_data['sortie_reelle']}'
    ";
  }
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

$ljoin["sejour"] = "sejour.patient_id = patients.patient_id";
$ljoin["dossier_medical"] = "dossier_medical.object_id = patients.patient_id";
$ljoin["operations"] = "operations.sejour_id = sejour.sejour_id";

$list_patient = $patient->loadList($where, "patients.nom, patients.prenom", 50, "patients.patient_id", $ljoin);
$count_patient = $patient->countList($where, null, null, null, $ljoin);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("one_field", $one_field);
$smarty->assign("list_patient", $list_patient);
$smarty->assign("count_patient", $count_patient);
$smarty->display("inc_recherche_dossier_clinique_results.tpl");
