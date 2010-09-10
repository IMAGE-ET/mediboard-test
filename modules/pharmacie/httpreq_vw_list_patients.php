<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage pharmacie
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$patients = array();
$sejours = array();
$patient_id = CValue::getOrSession('patient_id');
$service_id = CValue::getOrSession('service_id');

$date_min = CValue::get('_date_min');
$date_max = CValue::get('_date_max');

if (!$date_min) {
  $date_min = CValue::session('_date_delivrance_min');
}
if (!$date_max) {
  $date_max = CValue::session('_date_delivrance_max');
}

$date_min = "$date_min 00:00:00";
$date_max = "$date_max 23:59:59";

// Recherche des prescriptions dont les dates de sejours correspondent
$where = array();
$ljoin = array();
$ljoin['sejour'] = 'prescription.object_id = sejour.sejour_id';
$ljoin['affectation'] = 'sejour.sejour_id = affectation.sejour_id';
$ljoin['lit'] = 'affectation.lit_id = lit.lit_id';
$ljoin['chambre'] = 'lit.chambre_id = chambre.chambre_id';
$ljoin['service'] = 'chambre.service_id = service.service_id';
$where['prescription.type'] = " = 'sejour'";
$where[] = "(sejour.entree_prevue BETWEEN '$date_min' AND '$date_max') OR 
            (sejour.sortie_prevue BETWEEN '$date_min' AND '$date_max') OR
            (sejour.entree_prevue <= '$date_min' AND sejour.sortie_prevue >= '$date_max')"; 
$where['service.service_id'] = " = '$service_id'";

$prescription = new CPrescription();
$prescriptions = $prescription->loadList($where, null, null, null, $ljoin);

$tab_prescription_id = array();
if ($prescriptions) {
	foreach($prescriptions as &$_prescription){
		if(!$_prescription->_ref_object){
			$_prescription->loadRefObject();
		}
		$sejour =& $_prescription->_ref_object;
		$sejour->loadRefPatient();
		$patients[$sejour->patient_id] =& $sejour->_ref_patient;
		$sejours[$sejour->patient_id] = $sejour->_id;
		$tab_prescription_id[$sejour->patient_id] = $_prescription->_id;
	}
}

// Smarty template
$smarty = new CSmartyDP();
$smarty->assign("sejours"             , $sejours);
$smarty->assign("patients"            , $patients);
$smarty->assign("tab_prescription_id" , $tab_prescription_id);
$smarty->assign("patient_id"          , $patient_id);
$smarty->display('inc_vw_list_patients.tpl');

?>