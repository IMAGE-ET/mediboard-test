<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Romain Ollivier
*/

global $m, $tab;

CCanDo::checkEdit();

$protocole_id = CValue::getOrSession("protocole_id", 0);
$mediuser     = CMediusers::get();
$is_praticien = $mediuser->isPraticien();
$chir_id      = CValue::getOrSession("chir_id", $is_praticien ? $mediuser->user_id : null);

// Chargement du praticien
$chir = new CMediusers();
if ($chir_id) {
  $chir->load($chir_id);
}

// Vérification des droits sur les praticiens
$listPraticiens = $mediuser->loadPraticiens(PERM_EDIT);

$protocole = new CProtocole;
if($protocole_id) {
  $protocole->load($protocole_id);
  $protocole->loadRefs();
  $protocole->loadRefsNotes();

  // On vérifie que l'utilisateur a les droits sur l'operation
  if (!array_key_exists($protocole->chir_id, $listPraticiens)) {
    CAppUI::setMsg("Vous n'avez pas accès à ce protocole", UI_MSG_WARNING);
    CAppUI::redirect("m=$m&tab=$tab&protocole_id=0"); 
  }
  $chir =& $protocole->_ref_chir;
}


// Durée d'une intervention
$start = CAppUI::conf("dPplanningOp COperation duree_deb");
$stop  = CAppUI::conf("dPplanningOp COperation duree_fin");
$step  = CAppUI::conf("dPplanningOp COperation min_intervalle");

$hours = range($start, $stop);
$mins = range(0,59,$step);

// Récupération des services
$service = new CService();
$where = array();
$where["group_id"] = "= '".CGroups::loadCurrent()->_id."'";
$order = "nom";
$listServices = $service->loadListWithPerms(PERM_READ,$where, $order);


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("mediuser"    , $mediuser);
$smarty->assign("is_praticien", $is_praticien);
$smarty->assign("protocole"   , $protocole);
$smarty->assign("chir"        , $chir);

$smarty->assign("listPraticiens", $listPraticiens);
$smarty->assign("listServices"  , $listServices);

$smarty->assign("hours", $hours);
$smarty->assign("mins" , $mins);

$smarty->display("vw_edit_protocole.tpl");

?>