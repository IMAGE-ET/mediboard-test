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

$min = "$date_min 00:00:00";
$max = "$date_max 23:59:59";

$lines = array();

// Recherche des lignes de prescriptions de stupefiants
$prescription_line_medicament = new CPrescriptionLineMedicament();
$ljoin = array();
$ljoin["prescription"] = "prescription.prescription_id = prescription_line_medicament.prescription_id";
$ljoin["sejour"] = "prescription.object_id = sejour.sejour_id AND prescription.object_class = 'CSejour' AND prescription.type = 'sejour'";
$where = array();
$where[] = "(sejour.entree BETWEEN '$min' AND '$max') OR 
            (sejour.sortie BETWEEN '$min' AND '$max') OR
            (sejour.entree <= '$min' AND sejour.sortie >= '$max')";
$where["substitution_active"] = " = '1'";
$where["stupefiant"] = " = '1'";
$lines_med = $prescription_line_medicament->loadList($where, null, null, null, $ljoin);

$prescription_line_mix_item = new CPrescriptionLineMixItem();
$ljoin = array();
$ljoin["prescription_line_mix"] = "prescription_line_mix.prescription_line_mix_id = prescription_line_mix_item.prescription_line_mix_id";
$ljoin["prescription"] = "prescription.prescription_id = prescription_line_mix.prescription_id";
$ljoin["sejour"] = "prescription.object_id = sejour.sejour_id AND prescription.object_class = 'CSejour' AND prescription.type = 'sejour'";
$where = array();
$where[] = "(sejour.entree BETWEEN '$min' AND '$max') OR 
            (sejour.sortie BETWEEN '$min' AND '$max') OR
            (sejour.entree <= '$min' AND sejour.sortie >= '$max')";
$where["substitution_active"] = " = '1'";
$where["stupefiant"] = " = '1'";
$lines_mix_item = $prescription_line_mix_item->loadList($where, null, null, null, $ljoin);

foreach($lines_med as $_line_med){
	$lines[$_line_med->_view.$_line_med->_guid] = $_line_med;
}

foreach($lines_mix_item as $_line_mix_item){
  $lines[$_line_mix_item->_view.$_line_mix_item->_guid] = $_line_mix_item;
}

ksort($lines);
foreach($lines as $_line){
	if($_line instanceof CPrescriptionLineMedicament){
	  $_line->loadRefsPrises();
    $_line->_ref_prescription->_ref_object->loadRefPatient();
  } 
	else {
    	
  }
}
// Création du template
$smarty = new CSmartyDP();
$smarty->assign("lines", $lines);
$smarty->assign("date_min", $date_min);
$smarty->assign("date_max", $date_max);
$smarty->assign("print", $print);
$smarty->display('inc_vw_list_prescriptions_stup.tpl');

?>