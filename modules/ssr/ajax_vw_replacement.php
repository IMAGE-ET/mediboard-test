<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$sejour_id = CValue::getOrSession("sejour_id");
$conge_id = CValue::getOrSession("conge_id");
$type = CValue::getOrSession("type");

$sejour = new CSejour();
$sejour->load($sejour_id);

$conge = new CPlageVacances();
$conge->load($conge_id);

// Chargement d'un remplacement
$sejour->loadRefReplacement();
$replacement =& $sejour->_ref_replacement;

$sejour->loadRefPatient();
$patient =& $sejour->_ref_patient;

$replacements = new CReplacement();
$ljoin["sejour"] = "sejour.sejour_id = replacement.sejour_id";
$ljoin["patients"] = "sejour.patient_id = patients.patient_id";

$where = array();
$where["patients.patient_id"] = " = '$patient->_id'";
$replacements = $replacements->loadList($where, null, null, null, $ljoin);

foreach($replacements as $_replacement){
	$_replacement->loadRefReplacer();
	$_replacement->loadRefSejour();
}

if($replacement->_id){
	if(isset($replacements[$replacement->_id])){
		unset($replacements[$replacement->_id]);
  }
}


// Chargement des praticiens
$user = new CMediusers();
$users = $user->loadUsers();

if(!$replacement->_id){
  $replacement->conge_id = $conge_id;
  $replacement->sejour_id = $sejour_id;
}

if($type == 'kine'){
	// Chargement des evenements SSR dont le therapeute est le kine principal pendant sa periode de congé
	$sejour->loadRefBilanSSR();
	$bilan_ssr =& $sejour->_ref_bilan_ssr;
	
	$bilan_ssr->loadRefTechnicien();
	$kine_id = $bilan_ssr->_ref_technicien->kine_id;
	
	$evenement_ssr = new CEvenementSSR();
	$where = array();
	$where["therapeute_id"] = " = '$kine_id'";
	$where["sejour_id"] = " = '$sejour->_id'";
	$where["debut"] = "BETWEEN '$conge->date_debut' AND '$conge->date_fin'";
	$evenements = $evenement_ssr->loadList($where);
}

if($type == "reeducateur"){
  // Chargement des evenements SSR
	$evenement_ssr = new CEvenementSSR();
	$evenement_ssr->therapeute_id = $conge->user_id;
	$evenement_ssr->sejour_id = $sejour_id;
	$evenements = $evenement_ssr->loadMatchingList();
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("replacements", $replacements);
$smarty->assign("sejour", $sejour);
$smarty->assign("replacement", $replacement);
$smarty->assign("conge_id", $conge_id);
$smarty->assign("users", $users);
$smarty->assign("evenements", $evenements);
$smarty->assign("type", $type);
$smarty->display("inc_vw_replacement.tpl");

?>