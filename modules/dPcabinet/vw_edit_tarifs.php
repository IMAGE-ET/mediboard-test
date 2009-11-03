<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Thomas Despoix
*/

global $AppUI, $can, $m;
$can->needsEdit();

// Edite t'on un tarif ?
$tarif_id = CValue::getOrSession("tarif_id");
$tarif = new CTarif;
$tarif->load($tarif_id);
if (!$tarif->getPerm(PERM_EDIT)) {
  $AppUI->setMsg("Vous n'avez pas le droit de modifier ce tarif");
  $tarif = new CTarif;
}

// L'utilisateur est-il praticien ?
$user = $AppUI->_ref_user;
$user->loadRefFunction();

$prat = new CMediusers();
$prat->load($user->_id);

// Liste des tarifs du praticien
$listeTarifsChir = null;

$order = "description";
if ($user->isPraticien()) {
  $where = array();
  $where["function_id"] = "IS NULL";
  $where["chir_id"] = "= '$user->user_id'";
  $listeTarifsChir = $tarif->loadList($where, $order);
}

if ($user->isSecretaire()) {
  $prat_id = CValue::getOrSession("prat_id");
  
  // Toujours choisir le praticien du tarif choisi
  if ($tarif->_id && $tarif->chir_id) {
    $prat_id = $tarif->chir_id;
    CValue::setSession("prat_id", $prat_id);
  }
  
  if ($prat_id) {
    $prat->load($prat_id);
    $prat->loadRefFunction();
    $where = array();
    $where["function_id"] = "IS NULL";
    $where["chir_id"] = "= '$prat->_id'";
    $listeTarifsChir = $tarif->loadList($where, $order);
  }
}

if ($listeTarifsChir) {
  foreach ($listeTarifsChir as $_tarif) {
    $_tarif->getPrecodeReady();
  }
}

// Liste des tarifs de la spécialité
$where = array();
$where["chir_id"] = "IS NULL";
$where["function_id"] = "= '$prat->function_id'";
$listeTarifsSpe = new CTarif();
$listeTarifsSpe = $listeTarifsSpe->loadList($where, $order);
foreach ($listeTarifsSpe as $_tarif) {
  $_tarif->getPrecodeReady();
}

// Liste des praticiens du cabinet -> on ne doit pas voir les autres...
$listPrat = $user->_is_secretaire ?
  $user->loadPraticiens(PERM_READ) :
  array($user->_id => $user);
  
// Création du template
$smarty = new CSmartyDP();

$smarty->assign("user"           , $user);
$smarty->assign("listeTarifsChir", $listeTarifsChir);
$smarty->assign("listeTarifsSpe" , $listeTarifsSpe);
$smarty->assign("tarif"          , $tarif);
$smarty->assign("prat"           , $prat);
$smarty->assign("listPrat"       , $listPrat);

$smarty->display("vw_edit_tarifs.tpl");

?>