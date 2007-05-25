<?php 

/**
* @package Mediboard
* @subpackage dPgestionCab
* @version $Revision: 2012 $
* @author Poiron Yohann
*/

global $AppUI, $can, $m;

$can->needsRead();

$mode_paiement_id = mbGetValueFromGet("mode_paiement_id");

$modePaiement = new CModePaiement();
$modePaiement->load($mode_paiement_id);

// Récupération de la liste des functions
$listFunc = new CFunctions();
$listFunc = $listFunc->loadSpecialites(PERM_EDIT);

$where = array();
$itemModePaiement = new CModePaiement;
$order = "nom DESC";
 
// Récupération de la liste des mode de paiement hors fonction
$where["function_id"] = "IS NULL";
$listModePaiementGroup = $itemModePaiement->loadList($where,$order);
 
$listModePaiementFonction = array();

// Récupération de la liste des mode de paiement liés aux fonctions
foreach($listFunc as $function) {
	$where["function_id"] = "= $function->function_id";
	$listModePaiementFonction[$function->text] = $itemModePaiement->loadList($where,$order);
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("listFunc" 		   			, $listFunc);
$smarty->assign("modePaiement" 	   			, $modePaiement);
$smarty->assign("listModePaiementGroup" 	, $listModePaiementGroup);
$smarty->assign("listModePaiementFonction" , $listModePaiementFonction);

$smarty->display("edit_mode_paiement.tpl");

?>