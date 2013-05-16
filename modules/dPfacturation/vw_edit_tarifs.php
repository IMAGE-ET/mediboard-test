<?php 
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPfacturation
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

CCanDo::checkEdit();
// Edite t'on un tarif ?
$tarif_id = CValue::getOrSession("tarif_id");
$tarif = new CTarif;
$tarif->load($tarif_id);
if (!$tarif->getPerm(PERM_EDIT)) {
  CAppUI::setMsg("Vous n'avez pas le droit de modifier ce tarif");
  $tarif = new CTarif;
}
$tarif->loadRefsNotes();
$tarif->getSecteur1Uptodate();
$tarif->loadView();
$tarif->getPrecodeReady();

// L'utilisateur est-il praticien ?
$user = CAppUI::$user;
$user->loadRefFunction();

$prat = new CMediusers();
$prat->load($user->_id);
$prat->loadRefFunction();

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
    $_tarif->getSecteur1Uptodate();
  }
}

// Liste des tarifs de la spécialité
$where                = array();
$where["chir_id"]     = "IS NULL";
$where["function_id"] = "= '$prat->function_id'";

$listeTarifsSpe = new CTarif();
$listeTarifsSpe = $listeTarifsSpe->loadList($where, $order);
foreach ($listeTarifsSpe as $_tarif) {
  $_tarif->getPrecodeReady();
  $_tarif->getSecteur1Uptodate();
}

$listeTarifsEtab = array();
if (CAppUI::conf("dPcabinet Tarifs show_tarifs_etab")) {
  // Liste des tarifs de la spécialité
  $where = array();
  $where["chir_id"] = "IS NULL";
  $where["function_id"] = "IS NULL";
  $where["group_id"] = "= '".CGroups::loadCurrent()->_id."'";
  $listeTarifsEtab = new CTarif();
  $listeTarifsEtab = $listeTarifsEtab->loadList($where, $order);
  foreach ($listeTarifsEtab as $_tarif) {
    $_tarif->getPrecodeReady();
    $_tarif->getSecteur1Uptodate();
  }
}

// Liste des praticiens du cabinet -> on ne doit pas voir les autres...
if ($user->_is_secretaire) {
  $listPrat = CAppUI::pref("pratOnlyForConsult", 1) ?
    $user->loadPraticiens(PERM_READ) :
    $user->loadProfessionnelDeSante(PERM_READ);
}
else {
  $listPrat = array($user->_id => $user);
}

if (!$tarif->_id) {
  $tarif->secteur1 = 0;
}
// Création du template
$smarty = new CSmartyDP();

$smarty->assign("user"           , $user);
$smarty->assign("listeTarifsChir", $listeTarifsChir);
$smarty->assign("listeTarifsSpe" , $listeTarifsSpe);
$smarty->assign("listeTarifsEtab", $listeTarifsEtab);
$smarty->assign("tarif"          , $tarif);
$smarty->assign("prat"           , $prat);
$smarty->assign("listPrat"       , $listPrat);

$smarty->display("../../dPcabinet/templates/vw_edit_tarifs.tpl");