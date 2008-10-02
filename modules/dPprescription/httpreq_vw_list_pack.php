<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Alexis Granger
 */


$praticien_id = mbGetValueFromGetOrSession("praticien_id");
$function_id = mbGetValueFromGetOrSession("function_id");
$pack_id = mbGetValueFromGet("pack_id");

$pack = new CPrescriptionProtocolePack();
$pack->load($pack_id);


// Chargement du praticien
$praticien = new CMediusers();
$praticien->load($praticien_id);

$pack = new CPrescriptionProtocolePack();
$pack->load($pack_id);
$pack->loadRefPraticien();

// Initialisations
$packs_praticien = array();
$packs_function = array();

// Chargement des packs du praticien selectionne
if($praticien_id){
  $packPraticien = new CPrescriptionProtocolePack();
  $packPraticien->praticien_id = $praticien_id;
  $packs_praticien = $packPraticien->loadMatchingList();
}

// Chargement des packs du cabinet selectionne ou du cabinet du praticien
$_function_id = $function_id ? $function_id : $praticien->function_id;
if($_function_id){
  $packFunction = new CPrescriptionProtocolePack();
  $packFunction->function_id = $_function_id;
  $packs_function = $packFunction->loadMatchingList();
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("praticien_id", $praticien_id);
$smarty->assign("function_id", $function_id);
$smarty->assign("packs_praticien", $packs_praticien);
$smarty->assign("packs_function", $packs_function);
$smarty->assign("pack", $pack);
$smarty->display("inc_vw_list_pack.tpl");

?>