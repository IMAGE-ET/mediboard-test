<?php /* $Id: vw_bilan_prescription.php 6159 2009-04-23 08:54:24Z alexis_granger $ */

/**
 * @package Mediboard
 * @subpackage soins
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$categories_id = CValue::getOrSession("categories_id");
$date          = CValue::getOrSession("date");
$date_max      = mbDate("+ 1 DAY", $date);
$service_id    = CValue::getOrSession("service_id");

// Chargement des lignes de prescription
$line = new CPrescriptionLineElement();

$ljoin = array();
$ljoin["prescription"] = "prescription.prescription_id = prescription_line_element.prescription_id AND prescription.type = 'sejour'";
$ljoin["sejour"] = "sejour.sejour_id = prescription.object_id";
$ljoin["affectation"] = "sejour.sejour_id = affectation.sejour_id";
$ljoin["lit"] = "affectation.lit_id = lit.lit_id";
$ljoin["chambre"] = "lit.chambre_id = chambre.chambre_id";
$ljoin["service"] = "chambre.service_id = service.service_id";
  
$where = array();
$where["element_prescription_id"] =  CSQLDataSource::prepareIn($categories_id);
$where[] = "'$date' <= sejour.sortie && '$date_max' >= sejour.entree";
$where["service.service_id"] = " = '$service_id'";

$lines = $line->loadList($where, null, null, null, $ljoin);

// Chargement du patient pour chaque sejour
$sejours = CMbArray::pluck($lines, "_ref_prescription", "_ref_object");
$patients = CMbObject::massLoadFwdRef($sejours, "patient_id");

$lines_by_patient = array();
foreach($lines as $_line){
	if($date <= $_line->_debut_reel && $date <= $_line->_fin_reelle){
	  $_sejour = $_line->_ref_prescription->_ref_object;
    $_sejour->loadRefPatient();
    
		$_line->loadRefsPrises();
		$lines_by_patient[$_sejour->patient_id][$_line->_id] = $_line;
	}
}

// Smarty template
$smarty = new CSmartyDP();
$smarty->assign("lines", $lines);
$smarty->assign("patients", $patients);
$smarty->assign("lines_by_patient", $lines_by_patient);
$smarty->display('inc_vw_content_plan_soins_service.tpl');

?>