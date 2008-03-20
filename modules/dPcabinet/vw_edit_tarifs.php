<?php /* $Id: vw_compta.php 3483 2008-02-19 13:22:12Z alexis_granger $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: 3483 $
* @author Thomas Despoix
*/

global $AppUI, $can, $m, $utypes;
$can->needsEdit();

// Edite t'on un tarif ?
$tarif_id = mbGetValueFromGetOrSession("tarif_id", null);
$tarif = new CTarif;
$tarif->load($tarif_id);
if(!$tarif->getPerm(PERM_EDIT)) {
  $AppUI->setMsg("Vous n'avez pas le droit de modifier ce tarif");
  $tarif = new CTarif;
}

// L'utilisateur est-il praticien ?
$mediuser = new CMediusers();
$mediuser->load($AppUI->user_id);
$mediuser->loadRefFunction();

$tarifMediuser = new CMediusers();
$tarifMediuser->load($mediuser->_id);

// Liste des tarifs du praticien
$listeTarifsChir = null;

$is_praticien           = $mediuser->isPraticien();
$is_admin_or_secretaire = in_array($utypes[$mediuser->_user_type], array("Administrator", "Secrétaire"));

if($mediuser->isPraticien()) {
  $is_praticien = 1;
  $where = array();
  $where["function_id"] = "IS NULL";
  $where["chir_id"] = "= '$mediuser->user_id'";
  $listeTarifsChir = new CTarif();
  $listeTarifsChir = $listeTarifsChir->loadList($where);
}

if($mediuser->_user_type == 1 ||$mediuser->_user_type == 3) {
  $is_admin_or_secretaire = 1;
  $tarifPrat = mbGetValueFromGetOrSession("tarifPrat", null);
  if($tarifPrat) {
    $tarifMediuser->load($tarifPrat);
    $tarifMediuser->loadRefFunction();
    $where = array();
    $where["function_id"] = "IS NULL";
    $where["chir_id"] = "= '$tarifMediuser->_id'";
    $listeTarifsChir = new CTarif();
    $listeTarifsChir = $listeTarifsChir->loadList($where);
  }
}

// Liste des tarifs de la spécialité
$where = array();
$where["chir_id"] = "IS NULL";
$where["function_id"] = "= '$tarifMediuser->function_id'";
$listeTarifsSpe = new CTarif();
$listeTarifsSpe = $listeTarifsSpe->loadList($where);

// Liste des praticiens du cabinet -> on ne doit pas voir les autres...
$listPrat = in_array($utypes[$mediuser->_user_type], array("Administrator", "Secrétaire")) ?
  $mediuser->loadPraticiens(PERM_READ) :
  array($mediuser->_id => $mediuser);
  
// Création du template
$smarty = new CSmartyDP();

$smarty->assign("mediuser"              , $mediuser);
$smarty->assign("listeTarifsChir"       , $listeTarifsChir);
$smarty->assign("listeTarifsSpe"        , $listeTarifsSpe);
$smarty->assign("tarif"                 , $tarif);
$smarty->assign("tarifMediuser"         , $tarifMediuser);
$smarty->assign("is_praticien"          , $is_praticien);
$smarty->assign("is_admin_or_secretaire", $is_admin_or_secretaire);
$smarty->assign("listPrat"              , $listPrat);

$smarty->display("vw_edit_tarifs.tpl");

?>