<?php /* $Id: vw_bilan_prescription.php 6159 2009-04-23 08:54:24Z alexis_granger $ */

/**
 * @package Mediboard
 * @subpackage soins
 * @version $Revision: 6159 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can, $m, $AppUI;

$can->needsRead();

$praticien_id = CValue::getOrSession("praticien_id" , $AppUI->user_id);
$signee       = CValue::getOrSession("signee"       , 0);  // par default les non signees
$date_min     = CValue::getOrSession("_date_min"     , mbDateTime("00:00:00"));  // par default, date du jour
$date_max     = CValue::getOrSession("_date_max"     , mbDateTime("23:59:59"));
$type         = CValue::getOrSession("type"         , "sejour");  // sejour - externe - sortie_manquante

// Chargement de la liste des praticiens
$mediuser = new CMediusers();
$praticiens = $mediuser->loadPraticiens();

// Recherche des prescriptions
$where = array();
if($type == "sejour" || $type == "sortie_manquante"){
  $ljoin["sejour"] = "prescription.object_id = sejour.sejour_id";
  $ljoin["patients"] = "patients.patient_id = sejour.patient_id";
  $where["prescription.type"] = " = 'sejour'";
  $where[] = "(sejour.entree_prevue BETWEEN '$date_min' AND '$date_max') OR 
            (sejour.sortie_prevue BETWEEN '$date_min' AND '$date_max') OR
            (sejour.entree_prevue <= '$date_min' AND sejour.sortie_prevue >= '$date_max')"; 
} else {
  $where["prescription.type"] = " = 'externe'";
  $ljoin["consultation"] = "prescription.object_id = consultation.consultation_id";
  $ljoin["plageconsult"] = "consultation.plageconsult_id = plageconsult.plageconsult_id";
  $ljoin["patients"] = "patients.patient_id = consultation.patient_id";
  $where["plageconsult.date"] = "BETWEEN '$date_min' AND '$date_max'";
}

if($signee == "0"){ 
  if($praticien_id){
    $where[] = "(prescription_line_element.praticien_id = '$praticien_id' AND prescription_line_element.signee != '1') 
                OR (prescription_line_medicament.praticien_id = '$praticien_id' AND prescription_line_medicament.signee != '1' AND prescription_line_medicament.substitution_active = '1')
                OR (perfusion.praticien_id = '$praticien_id' AND perfusion.signature_prat != '1' AND perfusion.substitution_active = '1')";
  } else {
    $where[] = "(prescription_line_element.signee != '1') OR (prescription_line_medicament.signee != '1' AND prescription_line_medicament.substitution_active = '1') OR (perfusion.signature_prat != '1' AND perfusion.substitution_active = '1')";
  }
} else {
  if($praticien_id){
    $where[] = "(prescription_line_element.praticien_id = '$praticien_id') 
             OR (prescription_line_medicament.praticien_id = '$praticien_id')
             OR (perfusion.praticien_id = '$praticien_id')";
  }
}


$ljoin["prescription_line_element"] = "prescription_line_element.prescription_id = prescription.prescription_id";
$ljoin["prescription_line_medicament"] = "prescription_line_medicament.prescription_id = prescription.prescription_id";
$ljoin["perfusion"] = "perfusion.prescription_id = prescription.prescription_id";

$prescriptions = array();
$prescription = new CPrescription();
$order = "patients.nom";
$group_by = "prescription_id";
$prescriptions = $prescription->loadList($where, $order, null, $group_by, $ljoin);

if($type == "sortie_manquante"){
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

if(!$praticien_id){
  $user_courant = new CMediusers();
  $user_courant->load($AppUI->user_id);
  if($user_courant->isPraticien()){
    $praticien_id = $user_courant->_id;
  }
}

foreach($prescriptions as $_prescription){
	$_prescription->loadRefPatient();
}

// Smarty template
$smarty = new CSmartyDP();
$smarty->assign("prescriptions", $prescriptions);
$smarty->display('inc_vw_bilan_list_prescriptions.tpl');

?>