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

$consult_id = CValue::getOrSession("selConsult");
$user_id    = CValue::getOrSession("chirSel");
$_is_anesth = CValue::get("_is_anesth");

// Utilisateur sélectionné
$user = CMediusers::get($user_id);
$canUser = $user->canDo();
$canUser->needsEdit();

// Liste des praticiens
$listPrats = CConsultation::loadPraticiens(PERM_EDIT);

// Consultation courante
$consult = new CConsultation();
$consult->load($consult_id);
$consult->loadRefConsultAnesth();

CCanDo::checkObject($consult);
$canConsult = $consult->canDo();
$canConsult->needsEdit();

$consult->loadRefPatient();
$consult->_ref_patient->loadRefPhotoIdentite();
$consult->loadRefPraticien()->loadRefFunction();
$consult->loadRefSejour();

if (CModule::getActive("maternite")) {
  $consult->loadRefGrossesse();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("_is_anesth"    , $_is_anesth);
$smarty->assign("consult"       , $consult);
$smarty->assign("consult_anesth", $consult->_ref_consult_anesth);
$smarty->assign("listPrats"     , $listPrats);

$smarty->display("inc_finish_banner.tpl");
