<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$praticien_id = CValue::getOrSession("praticien_id");
$function_id  = CValue::getOrSession("function_id");
$group_id     = CValue::getOrSession("group_id");
$pack_id      = CValue::get("pack_id");

// Chargement du praticien
$praticien = new CMediusers();
$praticien->load($praticien_id);

// Chargement du pack
$pack = new CPrescriptionProtocolePack();
$pack->load($pack_id);
$pack->loadRefPraticien();

// Initialisations
$_packs = array();
$packs =
  array("prat"  => array(),
        "func"  => array(),
        "group" => array());

  
  
// Chargement des packs du praticien selectionné
if($praticien_id){
  $packPraticien = new CPrescriptionProtocolePack();
  $packPraticien->praticien_id = $praticien_id;
  $_packs['prat'] = $packPraticien->loadMatchingList();
}

// Chargement des packs du cabinet selectionné ou du cabinet du praticien
$_function_id = $function_id ? $function_id : $praticien->function_id;
$packFunction = new CPrescriptionProtocolePack();
$packFunction->function_id = $_function_id;
$_packs['func'] = $packFunction->loadMatchingList();

// Chargement des packs de l'établissement selectionné ou du courant
$_group_id = $group_id ? $group_id : CGroups::loadCurrent()->_id;
$packGroup = new CPrescriptionProtocolePack();
$packGroup->group_id = $_group_id;
$_packs["group"] = $packGroup->loadMatchingList();

// Classement des packs par object_class
foreach($_packs as $owner_pack => $_packs_by_owner){
  foreach($_packs_by_owner as $_pack){
    $packs[$owner_pack][$_pack->object_class][$_pack->_id] = $_pack;
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("praticien_id", $praticien_id);
$smarty->assign("function_id" , $function_id);
$smarty->assign("group_id"    , $group_id);
$smarty->assign("packs", $packs);
$smarty->assign("pack", $pack);
$smarty->display("inc_vw_list_pack.tpl");

?>