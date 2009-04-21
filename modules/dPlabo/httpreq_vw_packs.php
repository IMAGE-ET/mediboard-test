<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPlabo
 *	@version $Revision$
 *  @author Romain Ollivier
 */
 
global $AppUI, $can, $m;

$can->needsRead();
$ds = CSQLDataSource::get("std");
$user = new CMediusers;
$user->load($AppUI->user_id);

$pack_examens_labo_id = mbGetValueFromGetOrSession("pack_examens_labo_id");
$typeListe            = mbGetValueFromGetOrSession("typeListe");
$dragPacks            = mbGetValueFromGet("dragPacks", 0);

// Chargement des fontions
$function = new CFunctions;
$listFunctions = $function->loadListWithPerms(PERM_EDIT);

// Chargement du pack demandé
$pack = new CPackExamensLabo;
$pack->load($pack_examens_labo_id);
$pack->loadRefs();

//Chargement de tous les packs
$where = array("function_id IS NULL OR function_id ".$ds->prepareIn(array_keys($listFunctions)));
$where["obsolete"] = " = '0'";

$order = "libelle";
$listPacks = $pack->loadList($where, $order);
foreach($listPacks as $key => $curr_pack) {
  $listPacks[$key]->loadRefs();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("listPacks", $listPacks);
$smarty->assign("pack"     , $pack     );
$smarty->assign("dragPacks", $dragPacks);

$smarty->display("inc_vw_packs.tpl");

?>
