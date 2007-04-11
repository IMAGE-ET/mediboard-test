<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPlabo
 *	@version $Revision: $
 *  @author Romain Ollivier
 */
 
global $AppUI, $can, $m;

$can->needsRead();

$user = new CMediusers;
$user->load($AppUI->user_id);

// Chargement des fontions
$function = new CFunctions;
$listFunctions = $function->loadListWithPerms(PERM_EDIT);

//Chargement de tous les packs
$pack = new CPackExamensLabo;
$where = array("function_id IS NULL OR function_id ".db_prepare_in(array_keys($listFunctions)));
$order = "libelle";
$listPacks = $pack->loadList($where, $order);
foreach($listPacks as $key => $curr_pack) {
  $listPacks[$key]->loadRefs();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("listPacks", $listPacks);

$smarty->display("inc_vw_packs.tpl");

?>
