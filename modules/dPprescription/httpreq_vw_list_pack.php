<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$praticien_id = mbGetValueFromGetOrSession("praticien_id");
$function_id = mbGetValueFromGetOrSession("function_id");
$pack_id = mbGetValueFromGet("pack_id");

// Chargement du praticien
$praticien = new CMediusers();
$praticien->load($praticien_id);

// Chargement du pack
$pack = new CPrescriptionProtocolePack();
$pack->load($pack_id);
$pack->loadRefPraticien();

// Initialisations
$_packs = array();
$packs = array();

// Chargement des packs du praticien selectionne
if($praticien_id){
  $packPraticien = new CPrescriptionProtocolePack();
  $packPraticien->praticien_id = $praticien_id;
  $_packs['prat'] = $packPraticien->loadMatchingList();
}

// Chargement des packs du cabinet selectionne ou du cabinet du praticien
$_function_id = $function_id ? $function_id : $praticien->function_id;
if($_function_id){
  $packFunction = new CPrescriptionProtocolePack();
  $packFunction->function_id = $_function_id;
  $_packs['func'] = $packFunction->loadMatchingList();
}

// Classement des packs par object_class
foreach($_packs as $owner_pack => $_packs_by_owner){
  foreach($_packs_by_owner as $_pack){
    $packs[$owner_pack][$_pack->object_class][$_pack->_id] = $_pack;
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("praticien_id", $praticien_id);
$smarty->assign("function_id", $function_id);
$smarty->assign("packs", $packs);
$smarty->assign("pack", $pack);
$smarty->display("inc_vw_list_pack.tpl");

?>