<?php /* $Id:  $ */

/**
 * @package Mediboard
 * @subpackage dPsalleOp
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$operation_id = CValue::getOrSession("operation_id");

// Chargement de l'intervention
$operation = new COperation();
$operation->load($operation_id);

// Chargement des gestes operatoires
$operation->loadRefsAnesthPerops();

// Chargement des administrations per-op
$operation->loadRefSejour();
$sejour =& $operation->_ref_sejour;
$sejour->loadRefPrescriptionSejour();

$prescription_id = $sejour->_ref_prescription_sejour->_id;

$administration = new CAdministration();
$ljoin["prescription_line_medicament"] = "prescription_line_medicament.prescription_line_medicament_id = administration.object_id AND administration.object_class = 'CPrescriptionLineMedicament'";
$ljoin["prescription_line_element"] = "prescription_line_element.prescription_line_element_id = administration.object_id AND administration.object_class = 'CPrescriptionLineElement'";
$ljoin["prescription_line_mix_item"] = "prescription_line_mix_item.prescription_line_mix_item_id = administration.object_id AND administration.object_class = 'CPrescriptionLineMixItem'";
$ljoin["prescription_line_mix"] = "prescription_line_mix.prescription_line_mix_id = prescription_line_mix_item.prescription_line_mix_id";
                                                                                       
$ljoin["prescription"] = "(prescription_line_medicament.prescription_id = prescription.prescription_id) OR
                          (prescription_line_element.prescription_id = prescription.prescription_id) OR
                          (prescription_line_mix.prescription_id = prescription.prescription_id)";

$where["prescription.prescription_id"] = " = '$prescription_id'";

$where[] = "prescription_line_medicament.perop = '1' OR 
            prescription_line_element.perop = '1' OR
						prescription_line_mix.perop = '1'";
		
$administrations = $administration->loadList($where, null, null, null, $ljoin);

// Tri des gestes et administrations perop par ordre chronologique
$perops = array();
foreach($administrations as $_administration){
	$perops["$_administration->dateTime-$_administration->_guid"] = $_administration;
}
foreach($operation->_ref_anesth_perops as $_perop){
  $perops["$_perop->datetime-$_perop->_guid"] = $_perop;
}
ksort($perops);

$smarty = new CSmartyDP();
$smarty->assign("perops", $perops);
$smarty->assign("operation", $operation);
$smarty->display("print_intervention_anesth.tpl");

