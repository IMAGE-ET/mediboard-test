<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage pharmacie
 *  @version $Revision: $
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$date_min = CValue::get("date_min");
$date_max = CValue::get("date_max");
$print = CValue::get("print");
$service_id = CValue::get("service_id");

$min = $date_min;
$max = mbDate("+ 1 DAY", $date_max);

$order_way = CValue::getOrSession("order_way", "ASC");
$order_col = CValue::getOrSession("order_col", "patient_id");

$lines = array();
$group_id = CGroups::loadCurrent()->_id;

// Recherche des lignes de prescriptions de stupefiants
$prescription_line_medicament = new CPrescriptionLineMedicament();
$ljoin = array();
$ljoin["prescription"] = "prescription.prescription_id = prescription_line_medicament.prescription_id";
$ljoin["sejour"] = "prescription.object_id = sejour.sejour_id AND prescription.object_class = 'CSejour' AND prescription.type = 'sejour'";
$where = array();
$where[] = "sejour.entree <= '$max' AND sejour.sortie >= '$min'";
$where["substitution_active"] = " = '1'";
$where["stupefiant"] = " = '1'";
$where["sejour.group_id"] = " = '$group_id'";

if ($service_id) {
  $ljoin["affectation"] = "sejour.sejour_id = affectation.sejour_id";
  $ljoin["lit"]         = "affectation.lit_id = lit.lit_id";
  $ljoin["chambre"]     = "lit.chambre_id = chambre.chambre_id";
  $where["chambre.service_id"] = " = '$service_id'";
}

$lines_med = $prescription_line_medicament->loadList($where, null, null, null, $ljoin);

$prescription_line_mix_item = new CPrescriptionLineMixItem();
$ljoin = array();
$ljoin["prescription_line_mix"] = "prescription_line_mix.prescription_line_mix_id = prescription_line_mix_item.prescription_line_mix_id";
$ljoin["prescription"] = "prescription.prescription_id = prescription_line_mix.prescription_id";
$ljoin["sejour"] = "prescription.object_id = sejour.sejour_id AND prescription.object_class = 'CSejour' AND prescription.type = 'sejour'";
$where = array();
$where[] = "sejour.entree <= '$max' AND sejour.sortie >= '$min'";
$where["substitution_active"] = " = '1'";
$where["stupefiant"] = " = '1'";
$where["sejour.group_id"] = " = '$group_id'";
$lines_mix_item = $prescription_line_mix_item->loadList($where, null, null, null, $ljoin);

$lines = array_merge($lines_med, $lines_mix_item);

foreach($lines as $_line){
	if($_line instanceof CPrescriptionLineMedicament){
	  $_line->loadRefsPrises();
  } else {
  	$_line->_ref_prescription_line_mix->loadRefPraticien();
  }
	$_line->_ref_prescription->_ref_object->loadRefPatient();
}

if($order_col == "patient_id"){
  array_multisort(CMbArray::pluck($lines, "_ref_prescription", "_ref_object", "_ref_patient", "nom"), constant("SORT_$order_way"), $lines);
}

if($order_col == "_ucd_view"){
  array_multisort(CMbArray::pluck($lines, "_ucd_view"), constant("SORT_$order_way"), $lines);
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("lines", $lines);
$smarty->assign("date_min", $date_min);
$smarty->assign("date_max", $date_max);
$smarty->assign("print", $print);
$smarty->assign("order_col", $order_col);
$smarty->assign("order_way", $order_way);
$smarty->display('inc_vw_list_prescriptions_stup.tpl');

?>