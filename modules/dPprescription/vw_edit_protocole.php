<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI;

$praticien_id = CValue::getOrSession("praticien_id");
$function_id = CValue::getOrSession("function_id");
$group_id = CValue::getOrSession("group_id");
$protocole_id = CValue::get("prescription_id");

// Initialisations
$protocole = new CPrescription();
$protocoles = array();
$listFavoris = array();

// Chargement de la liste des praticiens
$praticien = new CMediusers();
$praticiens = $praticien->loadPraticiens(PERM_EDIT);

// Chargement du praticien selectionné
if($praticien_id){
  $praticien->load($praticien_id);
}

// Chargement des functions
$function = new CFunctions();
$functions = $function->loadSpecialites(PERM_EDIT);

// Chargement des etablissement
$groups = CGroups::loadGroups(PERM_EDIT);

// Chargement du protocole selectionné
if($protocole_id){
	$protocole->load($protocole_id);
	$protocole->loadRefsLinesMed();
	$protocole->loadRefsLinesElementByCat();
}

$contexteType = array();
$contexteType["CConsultation"][] = "externe";
$contexteType["CSejour"][] = "pre_admission";
$contexteType["CSejour"][] = "sortie";
$contexteType["CSejour"][] = "sejour";

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("today"          , mbDate());
$smarty->assign("contexteType"   , $contexteType);
$smarty->assign("praticien_id"   , $praticien_id);
$smarty->assign("praticiens"     , $praticiens);
$smarty->assign("functions"      , $functions);
$smarty->assign("groups"         , $groups);
$smarty->assign("group_id"       , $group_id);
$smarty->assign("praticien"      , $praticien);
//$smarty->assign("protocoles"     , $protocoles);
$smarty->assign("protocole"      , $protocole);
//$smarty->assign("listFavoris"    , $listFavoris);
$smarty->assign("function_id"    , $function_id);
$smarty->assign("protocoleSel_id", "");
$smarty->assign("mode_pharma"    , "0");
$smarty->assign("class_category" , new CCategoryPrescription());
$smarty->assign("mode_pack"      , "0");
$smarty->assign("protocole_id"   , $protocole_id);
$smarty->display("vw_edit_protocole.tpl");

?>