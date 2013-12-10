<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkEdit();
$tarif_id       = CValue::get("tarif_id");
$prat_id        = CValue::get("prat_id");
$codable_id     = CValue::get("codable_id");
$codable_class  = CValue::get("codable_class");

//Chargement du tarif
$tarif = new CTarif();
$tarif->load($tarif_id);

if (!$tarif->getPerm(PERM_EDIT) && $tarif_id) {
  CAppUI::setMsg("Vous n'avez pas le droit de modifier ce tarif");
  $tarif = new CTarif();
}
if ($codable_id) {
  $tarif->_bind_codable   = true;
  $tarif->_codable_id     = $codable_id;
  $tarif->_codable_class  = $codable_class;
  $tarif->bindCodable();
  $tarif->updateFormFields();
}
else {
  $tarif->loadRefsNotes();
  $tarif->getSecteur1Uptodate();
  $tarif->loadView();
  if (!$tarif->_id) {
    $tarif->secteur1 = 0;
  }
}

$tarif->getPrecodeReady();

// L'utilisateur est-il praticien ?
$user = CAppUI::$user;
$user->loadRefFunction();

// Liste des praticiens du cabinet -> on ne doit pas voir les autres
$listPrat = $user->isSecretaire() ? CConsultation::loadPraticiens(PERM_READ) : array($user->_id => $user);

//Chargement du praticien
$praticien_id = $tarif->chir_id ? $tarif->chir_id : $prat_id;
$prat = new CMediusers();
$prat->load($praticien_id);
$prat->loadRefFunction();

// Creation du template
$smarty = new CSmartyDP();

$smarty->assign("tarif",    $tarif);
$smarty->assign("user" ,    $user);
$smarty->assign("listPrat", $listPrat);
$smarty->assign("prat",     $prat);

$smarty->display("inc_edit_tarif.tpl");
