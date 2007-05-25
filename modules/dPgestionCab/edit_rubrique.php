<?php 

/**
* @package Mediboard
* @subpackage dPgestionCab
* @version $Revision: 2012 $
* @author Poiron Yohann
*/

global $AppUI, $can, $m;

$can->needsRead();

$user = new CMediusers();
$user->load($AppUI->user_id);
$user->loadRefsFwd();
$user->_ref_function->loadRefsFwd();

$etablissement = $user->_ref_function->_ref_group->text;

$rubrique_id = mbGetValueFromGet("rubrique_id");
 
$rubrique = new CRubrique();
$rubrique->load($rubrique_id);

// R�cup�ration de la liste des functions
$function = new CFunctions();
$listFunc = $function->loadListWithPerms(PERM_EDIT);

$where = array();
$itemRubrique = new CRubrique;
$order = "nom DESC";
 
// R�cup�ration de la liste des rubriques hors fonction
$where["function_id"] = "IS NULL";
$listRubriqueGroup = $itemRubrique->loadList($where,$order);
 
$listRubriqueFonction = array();

// R�cup�ration de la liste des rubriques li�s aux fonctions
foreach($listFunc as $function) {
	$where["function_id"] = "= $function->function_id";
	$listRubriqueFonction[$function->text] = $itemRubrique->loadList($where,$order);
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("etablissement"       	, $etablissement);
$smarty->assign("listFunc" 				, $listFunc);
$smarty->assign("rubrique" 				, $rubrique);
$smarty->assign("listRubriqueGroup" 	, $listRubriqueGroup);
$smarty->assign("listRubriqueFonction" , $listRubriqueFonction);

$smarty->display("edit_rubrique.tpl");

?>