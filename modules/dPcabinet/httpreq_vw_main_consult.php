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

// Utilisateur sélectionné ou utilisateur courant
$prat_id = CValue::getOrSession("chirSel", 0);

$userSel = CMediusers::get($prat_id);
$userSel->loadRefs();
$canUserSel = $userSel->canDo();

// Vérification des droits sur les praticiens
$listChir = CConsultation::loadPraticiens(PERM_EDIT);

if (!$userSel->isPraticien()) {
  CAppUI::setMsg("Vous devez selectionner un praticien", UI_MSG_ALERT);
  CAppUI::redirect("m=dPcabinet&tab=0");
}

$canUserSel->needsEdit();

$selConsult = CValue::getOrSession("selConsult");

// Consultation courante
$consult = new CConsultation();
$consult->_ref_chir = $userSel;
if ($selConsult) {
  $consult->load($selConsult);
  
  CCanDo::checkObject($consult);
  $canConsult = $consult->canDo();
  $canConsult->needsEdit();
  
  $consult->loadRefsFwd();
  $userSel->load($consult->_ref_plageconsult->chir_id);
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("consult", $consult);
$smarty->assign("userSel", $userSel);
$smarty->display("inc_main_consultform.tpl");

?>