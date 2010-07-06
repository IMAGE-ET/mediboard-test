<?php /* $Id: vw_idx_sejour.php 7212 2009-11-03 12:32:02Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7212 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

$date    = CValue::getOrSession("date", mbDate());
$kine_id = CValue::getOrSession("kine_id");

$mediuser = new CMediusers();
$mediuser->load($kine_id);

$planning = new CPlanningWeek($date);

// Sejour SSR
$sejour = new CSejour;

$order = "sejour.entree, sejour.sortie";

// Sejours pour lesquels le kine est référent
$join = array();
$join["bilan_ssr"]  = "bilan_ssr.sejour_id = sejour.sejour_id";
$join["technicien"] = "technicien.technicien_id = bilan_ssr.technicien_id";
$where = array();
$where["sejour.entree"] = "<= '$planning->date_max'";
$where["sejour.sortie"] = ">= '$planning->date_min'";
$where["technicien.kine_id"] = "= '$kine_id'";
$sejours["referenced"] = $sejour->loadList($where, null, null, null, $join);

// Sejours pour lesquels le kine est remplaçant
$join = array();
$join["replacement"]  = "replacement.sejour_id = sejour.sejour_id";
$where = array();
$where["sejour.entree"] = "<= '$planning->date_max'";
$where["sejour.sortie"] = ">= '$planning->date_min'";
$where["replacement.replacement_id"] = "IS NOT NULL";
$where["replacement.replacer_id"] = " = '$kine_id'";
$sejours["replaced"] = $sejour->loadList($where, null, null, null, $join);

// Sejours pour lesquels le rééducateur a des événements
$join["evenement_ssr"]  = "evenement_ssr.sejour_id = sejour.sejour_id";
$where = array();
$where["sejour.entree"] = "<= '$planning->date_max'";
$where["sejour.sortie"] = ">= '$planning->date_min'";
$where["evenement_ssr.therapeute_id"] = "= '$kine_id'";

$sejours["planned"] = $sejour->loadList($where, null, null, null, $join);

// Sejours pour lesquels le rééducateur est exécutant pour des lignes prescrites mais n'a pas encore d'evenement planifiés
$sejours["plannable"] = array();

$where = array();
$where["sejour.entree"] = "<= '$planning->date_max'";
$where["sejour.sortie"] = ">= '$planning->date_min'";
$_sejours = $sejour->loadList($where);

foreach($_sejours as $_sejour){
	$_sejour->loadRefBilanSSR();
	$bilan_ssr =& $_sejour->_ref_bilan_ssr;
	$bilan_ssr->loadRefTechnicien();
	$kine_referent_id = $bilan_ssr->_ref_technicien->kine_id;
	
	if(($kine_referent_id == $kine_id) && !array_key_exists($_sejour->_id, $sejours["planned"])){
    $sejours["plannable"][$_sejour->_id] = $_sejour;      		
	}

	// Chargement de la prescrtiption su sejour
	$_sejour->loadRefPrescriptionSejour();
	$prescription =& $_sejour->_ref_prescription_sejour;
	// Chargement des lignes de la prescription
	$prescription->loadRefsLinesElementByCat();
	// Parcours des lignes d'element
	foreach($prescription->_ref_prescription_lines_element as $_line_element){
		// Si le sejour est deja dans la liste des sejours
		if(array_key_exists($_sejour->_id, $sejours["plannable"])){
			continue;
		}
		
		// Recuperation de la categorie de prescription de la ligne
		$category_prescription_id = $_line_element->_ref_element_prescription->category_prescription_id;
		
		// Chargement des associations 
		$function_categorie = new CFunctionCategoryPrescription();
	  $function_categorie->category_prescription_id = $category_prescription_id;
	  $functions_cat = $function_categorie->loadMatchingList();
	  
		$functions = array();
		foreach($functions_cat as $function_cat){
		  if(($mediuser->function_id == $function_cat->function_id) && !array_key_exists($_sejour->_id, $sejours["planned"])){
		    $sejours["plannable"][$_sejour->_id] = $_sejour;      
		  }	
		}
	}
}

foreach ($sejours as &$_sejours) {
	foreach ($_sejours as $_sejour) {
		$_sejour->loadRefPatient();
		$_sejour->countEvenementsSSRWeek($kine_id, $planning->date_min, $planning->date_max);
	}
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("sejours", $sejours);
$smarty->display("inc_board_sejours.tpl");

?>