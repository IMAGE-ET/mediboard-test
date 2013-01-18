<?php /* $Id: vw_bilan_prescription.php 6159 2009-04-23 08:54:24Z alexis_granger $ */

/**
 * @package Mediboard
 * @subpackage soins
 * @version $Revision: 6159 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();
$user = CMediusers::get();

$praticien_id      = CValue::getOrSession("prat_bilan_id"      , $user->_id);
$signee            = CValue::getOrSession("signee"             , 0);         // par default les non signees
$date_min          = CValue::getOrSession("_date_entree_prevue", mbDate());  // par default, date du jour
$date_max          = CValue::getOrSession("_date_sortie_prevue", mbDate());
$type_prescription = CValue::getOrSession("type_prescription"  , "sejour");  // sejour - externe - sortie_manquante
$board             = CValue::get("board", 0);

$date_min = $date_min . " 00:00:00";
$date_max = $date_max . " 23:59:59";

// Chargement de la liste des praticiens
$mediuser = new CMediusers();
$praticiens = $mediuser->loadPraticiens();

$prescriptions = array();
$prescription = new CPrescription();

// Recherche des prescriptions
$where = array();
if ($type_prescription == "sejour" || $type_prescription == "sortie_manquante") {
  $ljoin["sejour"] = "prescription.object_id = sejour.sejour_id";
  $ljoin["patients"] = "patients.patient_id = sejour.patient_id";
  
  $where["prescription.type"] = " = 'sejour'";
  $where["sejour.entree"]     = " <= '$date_max'";
  $where["sejour.sortie"]     = " >= '$date_min'";
}
else {
  $ljoin["consultation"] = "prescription.object_id = consultation.consultation_id";
  $ljoin["plageconsult"] = "consultation.plageconsult_id = plageconsult.plageconsult_id";
  $ljoin["patients"] = "patients.patient_id = consultation.patient_id";
  
  $where["prescription.type"] = " = 'externe'";
  $where["plageconsult.date"] = "BETWEEN '$date_min' AND '$date_max'";
}

$ljoin_save = $ljoin;
$wheres = array();

$leftjoins = array(
  array("prescription_line_element",    "prescription_line_element.prescription_id = prescription.prescription_id"),
  array("prescription_line_medicament", "prescription_line_medicament.prescription_id = prescription.prescription_id"),
  array("prescription_line_mix",        "prescription_line_mix.prescription_id = prescription.prescription_id"),
);

if ($signee == "0") {
  if ($praticien_id) {
    $wheres = array(
      "prescription_line_element.praticien_id    = '$praticien_id' AND prescription_line_element.signee     != '1'",
      "prescription_line_medicament.praticien_id = '$praticien_id' AND prescription_line_medicament.signee  != '1' AND prescription_line_medicament.variante_active = '1'",
      "prescription_line_mix.praticien_id        = '$praticien_id' AND prescription_line_mix.signature_prat != '1' AND prescription_line_mix.variante_active = '1'",
    );
  }
  else {
    $wheres = array(
      "prescription_line_element.signee     != '1'",
      "prescription_line_medicament.signee  != '1' AND prescription_line_medicament.variante_active = '1'",
      "prescription_line_mix.signature_prat != '1' AND prescription_line_mix.variante_active = '1'",
    );
  }
}
else {
  if ($praticien_id) {
    $wheres = array(
      "prescription_line_element.praticien_id    = '$praticien_id'", 
      "prescription_line_medicament.praticien_id = '$praticien_id'", 
      "prescription_line_mix.praticien_id        = '$praticien_id'",
    );
  }
}

if (count($wheres)) {
  $keys = array();
  foreach($wheres as $_i => $_where) {
    $where[0] = $_where;
    
    // Pour ne pas afficher les prescriptions en double (== group by prescription_id)
    if (count($keys)) {
      $where["prescription.prescription_id"] = $prescription->_spec->ds->prepareNotIn($keys);
    }
      
    $ljoin[$leftjoins[$_i][0]] = $leftjoins[$_i][1];
    $_prescriptions = $prescription->loadList($where, null, null, null, $ljoin);
    
    $keys = array_merge($keys, array_keys($_prescriptions));
    $prescriptions = array_merge($prescriptions, $_prescriptions);
    
    $ljoin = $ljoin_save;
  }
  
  foreach ($prescriptions as $_presc) {
    $_presc->_ref_object->loadRefPatient();
  }
  array_multisort(CMbArray::pluck($prescriptions, "_ref_object", "_ref_patient", "nom"), SORT_ASC, $prescriptions);
}
else {
  $prescriptions = $prescription->loadList($where, "patients.nom", null, "prescription_id", $ljoin);
}

if($type_prescription == "sortie_manquante"){
  foreach($prescriptions as $_prescription){
    // Recherche d'une prescription de sortie correspondant à la prescription de sejour
    $_prescription_sortie = new CPrescription();
    $_prescription_sortie->type = "sortie";
    $_prescription_sortie->object_id = $_prescription->object_id;
    $_prescription_sortie->object_class = $_prescription->object_class;
    $_prescription_sortie->loadMatchingObject();
    if($_prescription_sortie->_id){
      unset($prescriptions[$_prescription->_id]);
    }
  }
}

$sejour = new CSejour();
$sejour->_date_min = $date_min;
$sejour->_date_max = $date_max;

if(!$praticien_id && $user->isPraticien()){
  $praticien_id = $user->_id;
}

foreach($prescriptions as $_prescription){
  $_prescription->loadRefPatient();
  
  $patient = $_prescription->_ref_patient;  
  $sejour = $_prescription->_ref_object;
  
  $patient->loadIPP();
  $patient->loadRefPhotoIdentite();
 
  $sejour->loadRefPraticien();
  $sejour->checkDaysRelative(mbDate());
  $sejour->loadSurrAffectations($date_min);
  $sejour->loadNDA();
    
  if ($_prescription->_id) {
    $_prescription->loadJourOp(mbDate());
  }

  $patient->loadRefDossierMedical();
  $dossier_medical = $patient->_ref_dossier_medical;
  
  if($dossier_medical->_id){
    $dossier_medical->loadRefsAllergies();
    $dossier_medical->loadRefsAntecedents();
    $dossier_medical->countAntecedents();
    $dossier_medical->countAllergies();
  }
}

// Smarty template
$smarty = new CSmartyDP();
$smarty->assign("prescriptions", $prescriptions);
$smarty->assign("board"        , $board);
$smarty->assign("date", $date_min);
$smarty->assign("default_tab", "prescription_sejour");
$smarty->display('inc_vw_bilan_list_prescriptions.tpl');

?>