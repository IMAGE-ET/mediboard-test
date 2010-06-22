<?php /* $Id: vw_aed_rpu.php 7346 2009-11-16 22:51:04Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7346 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkRead();

global $AppUI;

$sejour_id = CValue::getOrSession("sejour_id");

$user = new CMediusers();
$prats = $user->loadPraticiens(PERM_READ);

$sejour = new CSejour;
$sejour->load($sejour_id);

if ($sejour_id && !$sejour->_id) {
  CAppUI::setMsg(CAppUI::tr("CSejour-unavailable"), UI_MSG_WARNING);
  CAppUI::redirect("m=ssr&tab=vw_aed_sejour&sejour_id=0");
}

$fiche_autonomie = new CFicheAutonomie;
$patient = new CPatient;
$bilan = new CBilanSSR;
$prescription_SSR = new CPrescription();
$lines = array();

if ($sejour->_id) {
  $sejour->loadRefPatient();
  $sejour->loadNumDossier();

  // Chargement du patient
  $patient = $sejour->_ref_patient;
  $patient->loadStaticCIM10($AppUI->user_id);
  $patient->loadIPP();

  // Fiche autonomie  
  $fiche_autonomie->sejour_id = $sejour->_id;
  $fiche_autonomie->loadMatchingObject();
	
  // Bilan SSR  
  $bilan->sejour_id = $sejour->_id;
  $bilan->loadMatchingObject();
  
	// Prescription SSR
  $prescription_SSR->object_id = $sejour->_id;
	$presctiption_SSR->object_class = "CSejour";
	$prescription_SSR->type = "sejour";
	$prescription_SSR->loadMatchingObject();
	
	// Chargement des lignes de la prescription
	if ($prescription_SSR->_id){
		$line = new CPrescriptionLineElement();
		$line->prescription_id = $prescription_SSR->_id;
		$_lines = $line->loadMatchingList("debut ASC");
		foreach($_lines as $_line){
			$lines[$_line->_ref_element_prescription->category_prescription_id][] = $_line;
		}
	}
} 
else {
	$sejour->group_id = CGroups::loadCurrent()->_id;
  $sejour->praticien_id = $AppUI->user_id;
  $sejour->entree_prevue = mbDate()." 08:00:00";
  $sejour->sortie_prevue = mbDate()." 18:00:00";
}

// Aides à la saisie
$sejour->loadAides($AppUI->user_id);

$traitement = new CTraitement();
$traitement->loadAides($AppUI->user_id);

$antecedent = new CAntecedent();
$antecedent->loadAides($AppUI->user_id);

// Chargement des categories de prescription
$categories = array();
$category = new CCategoryPrescription();
$where[] = "chapitre = 'kine' OR chapitre = 'soin' OR chapitre = 'consult'";
$group_id = CGroups::loadCurrent()->_id;
$where[] = "group_id = '$group_id' OR group_id IS NULL";

$order = "nom";
$categories = $category->loadList($where, $order);

// Dossier médical visibile ?
$can_view_dossier_medical = 
  CModule::getCanDo('dPcabinet')->edit ||
  CModule::getCanDo('dPbloc')->edit ||
  CModule::getCanDo('dPplanningOp')->edit || 
  $AppUI->_ref_user->isFromType(array("Infirmière"));

$can_edit_prescription = $AppUI->_ref_user->isPraticien() || $AppUI->_ref_user->isAdmin();

// Suppression des categories vides
if(!$can_edit_prescription){
	foreach($categories as $_cat_id => $_category){
		if(!array_key_exists($_cat_id, $lines)){
		  unset($categories[$_cat_id]);	
		}
	}
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("can_view_dossier_medical", $can_view_dossier_medical);
$smarty->assign("today"               , mbDate());
$smarty->assign("traitement"          , $traitement);
$smarty->assign("antecedent"          , $antecedent);
$smarty->assign("sejour"              , $sejour);
$smarty->assign("fiche_autonomie"     , $fiche_autonomie);
$smarty->assign("bilan"               , $bilan);
$smarty->assign("patient"             , $patient);
$smarty->assign("prats"               , $prats);
$smarty->assign("categories"          , $categories);
$smarty->assign("prescription_SSR"    , $prescription_SSR);
$smarty->assign("lines"               , $lines);
$smarty->assign("can_edit_prescription", $can_edit_prescription);
$smarty->display("vw_aed_sejour_ssr.tpl");
?>