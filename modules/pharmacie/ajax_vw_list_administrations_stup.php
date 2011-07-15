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

$min = "$date_min 00:00:00";
$max = "$date_max 23:59:59";

$order_way = CValue::getOrSession("order_way", "ASC");
$order_col = CValue::getOrSession("order_col", "patient_id");

if (!$order_way) {
	$order_way = "ASC";
}

$group_id = CGroups::loadCurrent()->_id;

$where_default = array();
$where_default["sejour.group_id"] = " = '$group_id'";
$where_default["administration.dateTime"] = " BETWEEN  '$min' AND '$max'";
$where_default["planification"] = " = '0'";

$ljoin_service = array();
if ($service_id) {
  $ljoin_service["affectation"] = "sejour.sejour_id = affectation.sejour_id";
  $ljoin_service["lit"]         = "affectation.lit_id = lit.lit_id";
  $ljoin_service["chambre"]     = "lit.chambre_id = chambre.chambre_id";
  $ljoin_service["service"]     = "chambre.service_id = service.service_id";

  // Recupération de l'affectation courante
  $where_default["affectation.entree"] = " <= '$max'";
	$where_default["affectation.sortie"] = " >= '$min'";				
  $where_default["service.service_id"] = " = '$service_id'";
}

$administration = new CAdministration();

// Chargement des administrations de stupefiants dans les lignes de medicament
$ljoin = array();
$ljoin["prescription_line_medicament"] = "(prescription_line_medicament.prescription_line_medicament_id = administration.object_id) AND 
                                          (administration.object_class = 'CPrescriptionLineMedicament')";
$ljoin["prescription"] = "(prescription_line_medicament.prescription_id = prescription.prescription_id)";               
$ljoin["sejour"] = "prescription.object_id = sejour.sejour_id AND prescription.object_class = 'CSejour'";
$ljoin += $ljoin_service;

$where_med = $where_default;
$where_med[] = "prescription_line_medicament.stupefiant = '1'";
$administrations_med = $administration->loadList($where_med, null, null, null, $ljoin);

// Chargement des administrations de stupefiants dans les mix items
$ljoin = array();
$ljoin["prescription_line_mix_item"] = "(prescription_line_mix_item.prescription_line_mix_item_id = administration.object_id) AND
                                        (administration.object_class = 'CPrescriptionLineMixItem')";                                          
$ljoin["prescription_line_mix"] = "prescription_line_mix.prescription_line_mix_id = prescription_line_mix_item.prescription_line_mix_id";                                                                                 
$ljoin["prescription"] = "(prescription_line_mix.prescription_id = prescription.prescription_id)";                  
$ljoin["sejour"] = "prescription.object_id = sejour.sejour_id AND prescription.object_class = 'CSejour'";
$ljoin += $ljoin_service;

$where_mix = $where_default;
$where_mix[] = "prescription_line_mix_item.stupefiant = '1'";

$administrations_mix = $administration->loadList($where_mix, null, null, null, $ljoin);

// Fusion des tableaux d'administrations
$administrations = array_merge($administrations_med, $administrations_mix);

// Parcours des administrations et chargements des refs
foreach($administrations as &$_administration){
  $_administration->loadTargetObject();
	$_administration->loadRefAdministrateur();
  
  $prescription =& $_administration->_ref_object->_ref_prescription;
	$prescription->_ref_object->loadRefPatient();
	
	if($_administration->_ref_object instanceof CPrescriptionLineMixItem){
		$_administration->_ref_object->_ref_prescription_line_mix->loadRefPraticien();
	}
}

// Association des adm aux sejours
$sejours = CMbArray::pluck($administrations, "_ref_object", "_ref_prescription", "_ref_object");

$sorter = $sejours;

// Tri par patient
if ($order_col == "patient_id") {
	$sorter = CMbArray::pluck($sejours, "_ref_patient", "nom");
}

// Tri par date
if ($order_col == "dateTime") {
	$sorter = CMbArray::pluck($administrations, "dateTime");
}

// Tri par libelle de produit
if($order_col == "_ucd_view"){
	$sorter = CMbArray::pluck($administrations, "_ref_object", "_view");
}

array_multisort($sorter, constant("SORT_$order_way"), $administrations);
	
// Création du template
$smarty = new CSmartyDP();
$smarty->assign("administrations", $administrations);
$smarty->assign("date_min", $date_min);
$smarty->assign("date_max", $date_max);
$smarty->assign("print", $print);
$smarty->assign("order_col", $order_col);
$smarty->assign("order_way", $order_way);
$smarty->display('inc_vw_list_administrations_stup.tpl');

?>