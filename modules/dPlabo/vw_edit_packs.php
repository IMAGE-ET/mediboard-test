<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPlabo
 *	@version $Revision$
 *  @author Romain Ollivier
 */
 
global $AppUI, $can, $m;

$can->needsRead();

$user = new CMediusers;
$user->load($AppUI->user_id);

// Chargement des fontions
$function = new CFunctions;
$listFunctions = $function->loadListWithPerms(PERM_EDIT);

// Chargement du pack demandé
$pack = new CPackExamensLabo;
$pack->load(CValue::getOrSession("pack_examens_labo_id"));
if($pack->_id && $pack->getPerm(PERM_EDIT)) {
  $pack->loadRefs();
} else {
  $pack = new CPackExamensLabo;
}

//Chargement de tous les packs
$where = array("function_id IS NULL OR function_id ".CSQLDataSource::prepareIn(array_keys($listFunctions)));
$where["obsolete"] = " = '0'";
$order = "libelle";
$listPacks = $pack->loadList($where, $order);
foreach($listPacks as $key => $curr_pack) {
  $listPacks[$key]->loadRefs();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("listFunctions", $listFunctions);
$smarty->assign("listPacks"    , $listPacks    );
$smarty->assign("pack"         , $pack         );

$smarty->display("vw_edit_packs.tpl");
?>
