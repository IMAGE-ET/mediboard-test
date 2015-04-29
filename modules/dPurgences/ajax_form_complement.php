<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Urgences
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();
$rpu_id       = CValue::getOrSession("rpu_id");
$sejour_id    = CValue::get("sejour_id");

$rpu    = new CRPU;
if ($rpu_id && !$rpu->load($rpu_id)) {
  global $m, $tab;
  CAppUI::setMsg("Ce RPU n'est pas ou plus disponible", UI_MSG_WARNING);
  CAppUI::redirect("m=$m&tab=$tab&rpu_id=0");
}

// Création d'un RPU pour un séjour existant
if ($sejour_id) {
  $rpu = new CRPU;
  $rpu->sejour_id = $sejour_id;
  $rpu->loadMatchingObject();
  $rpu->updateFormFields();
}

if ($rpu->_id || $rpu->sejour_id) {
  // Mise en session de l'id de la consultation, si elle existe.
  $rpu->loadRefConsult();
  if ($rpu->_ref_consult->_id) {
    CValue::setSession("selConsult", $rpu->_ref_consult->_id);
  }
  $sejour  = $rpu->_ref_sejour;
  $sejour->loadNDA();
  $sejour->loadRefPraticien(1);
  $sejour->loadRefsNotes();
}
else {
  $rpu->_entree         = CMbDT::dateTime();
  $sejour               = new CSejour;
}

$rpu->loadRefMotif();
$rpu->orderCtes();

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("rpu" , $rpu);

$smarty->display("inc_form_complement.tpl");
