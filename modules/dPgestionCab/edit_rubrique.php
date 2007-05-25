<?php 

/**
* @package Mediboard
* @subpackage dPgestionCab
* @version $Revision: 2012 $
* @author Poiron Yohann
*/

global $AppUI, $can, $m;

$rubrique_id = mbGetValueFromGet("rubrique_id");
 
$rubrique = new CRubrique();
$rubrique->load($rubrique_id);


// Récupération de la liste des functions
$listFunc = new CFunctions();
$listFunc = $listFunc->loadSpecialites(PERM_EDIT);

$where = array();
$itemRubrique = new CRubrique;
$order = "nom DESC";
 
// Récupération de la liste des rubriques hors fonction
$where["function_id"] = "IS NULL";
$listRubriqueGroup = $itemRubrique->loadList($where,$order);
 
$listRubriqueFonction = array();

// Récupération de la liste des rubriques liés aux fonctions
foreach($listFunc as $function) {
	$where["function_id"] = "= $function->function_id";
	$listRubriqueFonction[$function->text] = $itemRubrique->loadList($where,$order);
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("listFunc" 				, $listFunc);
$smarty->assign("rubrique" 				, $rubrique);
$smarty->assign("listRubriqueGroup" 	, $listRubriqueGroup);
$smarty->assign("listRubriqueFonction" , $listRubriqueFonction);

$smarty->display("edit_rubrique.tpl");

?>