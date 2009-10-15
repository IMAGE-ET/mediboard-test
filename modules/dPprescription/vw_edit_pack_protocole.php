<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */


$praticien_id = mbGetValueFromGetOrSession("praticien_id", CAppUI::$instance->user_id);
$function_id = mbGetValueFromGetOrSession("function_id");

$pack_id = mbGetValueFromGet("pack_id");

$packs = array();
$pack = new CPrescriptionProtocolePack();

// Chargement de la liste des praticiens
$praticien = new CMediusers();
$praticiens = $praticien->loadPraticiens();
$praticien->load($praticien_id);

// Chargement des functions
$function = new CFunctions();
$functions = $function->loadSpecialites(PERM_EDIT);

$pack = new CPrescriptionProtocolePack();
$pack->load($pack_id);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("praticien_id"   , $praticien_id);
$smarty->assign("function_id"    , $function_id);
$smarty->assign("praticiens"     , $praticiens);
$smarty->assign("functions"      , $functions);
$smarty->assign("pack", $pack);
$smarty->display("vw_edit_pack_protocole.tpl");

?>