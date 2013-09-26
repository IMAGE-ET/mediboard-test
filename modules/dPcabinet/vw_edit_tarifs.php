<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkEdit();

$tarif = new CTarif();

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
$listPrat = $user->isSecretaire() ? CConsultation::loadPraticiens(PERM_READ) : array($user->_id => $user);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("user"           , $user);
$smarty->assign("listeTarifsChir", $listeTarifsChir);
$smarty->assign("listeTarifsSpe" , $listeTarifsSpe);
$smarty->assign("listeTarifsEtab", $listeTarifsEtab);
$smarty->assign("tarif"          , $tarif);
$smarty->assign("prat"           , $prat);
$smarty->assign("listPrat"       , $listPrat);

$smarty->display("vw_edit_tarifs.tpl");