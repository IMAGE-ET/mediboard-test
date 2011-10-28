<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$user_id = CValue::getOrSession("user_id");

$where = array();
if($user_id){
  $user = new CMediusers();
  $user->load($user_id);
  
  if($user->isInfirmiere()){
    $where["prescriptible_infirmiere"] = " = '1'";
  } elseif ($user->isAideSoignant()){
    $where["prescriptible_AS"] = " = '1'";
  } elseif ($user->isKine()){
    $where["prescriptible_kine"] = " = '1'";
  }
}

// chargement des categories de prescription et des elements associés
$chapitres = CCategoryPrescription::loadCategoriesByChap(null, "current");
foreach($chapitres as &$categories){
	foreach($categories as $key=>&$category){
	  if ($category->only_cible) {
	    unset($categories[$key]); continue;
	  }
		$category->loadElementsPrescription(false, $where);
	}
}

$prescription_id = CValue::get("prescription_id");
$mode_protocole  = CValue::get("mode_protocole");
$mode_pharma     = CValue::get("mode_pharma");
$chapitre        = CValue::get("chapitre");


// chargement de la prescription
$prescription = new CPrescription();
$prescription->load($prescription_id);
$prescription->loadRefsLinesElement();

$elements = array();
foreach($prescription->_ref_prescription_lines_element as $_line_element){
	$elements[] = $_line_element->element_prescription_id; 
}

if($prescription->_ref_object instanceof CSejour){
	// Chargement des dates des operations
	$sejour =& $prescription->_ref_object;
	$sejour->makeDatesOperations();
}

// Chargement de la liste des moments
$moments = CMomentUnitaire::loadAllMomentsWithPrincipal();

$filter_line_element = new CPrescriptionLineElement();
$filter_line_element->debut = mbDate();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("elements", $elements);
$smarty->assign("chapitres", $chapitres);
$smarty->assign("filter_line_element", $filter_line_element);
$smarty->assign("moments", $moments);
$smarty->assign("prise_posologie", new CPrisePosologie());
$smarty->assign("class_category", new CCategoryPrescription());
$smarty->assign("prescription", $prescription);
$smarty->assign("prescription_id", $prescription_id);
$smarty->assign("mode_protocole", $mode_protocole);
$smarty->assign("mode_pharma", $mode_pharma);
$smarty->assign("chapitre", $chapitre);
$smarty->assign("today"      , mbDate());
$smarty->assign("is_praticien", CAppUI::$user->isPraticien());
$smarty->display("../../dPprescription/templates/vw_easy_mode.tpl");

?>