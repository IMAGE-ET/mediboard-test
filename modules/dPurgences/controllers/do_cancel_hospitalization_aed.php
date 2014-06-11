<?php 

/**
 * $Id$
 *  
 * @category dPurgences
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

$sejour_guid = CValue::post("sejour_guid");

/** @var CSejour $sejour */
$sejour = CMbObject::loadFromGuid($sejour_guid);

if ($sejour && !$sejour->_id) {
  CAppUI::setMsg("Sejour non renseigné", UI_MSG_ERROR);
}

$rpu             = $sejour->loadRefRPU();
$sejour_mutation = $rpu->loadRefSejourMutation();

//On annule le relicat
$sejour->mode_sortie   = "";
$sejour->entree_reelle = "";
$sejour->annule        = "1";
if ($msg = $sejour->store()) {
  CAppUI::setMsg($msg, UI_MSG_ERROR);
  return;
}

//On remet le séjour d'hospi en séjour d'urgence
$sejour_mutation->type   = "urg";
$rpu->sejour_id          = $sejour_mutation->_id;
$rpu->mutation_sejour_id = "";
$rpu->sortie_autorisee   = "0";
$rpu->gemsa              = "";

//On supprime les affectations d'hospi
$affectations = $sejour_mutation->loadRefsAffectations();

foreach ($affectations as $_affectation) {
  $service = $_affectation->loadRefService();
  if ($service->uhcd || $service->radiologie || $service->urgence) {
    continue;
  }
  if ($msg = $_affectation->delete()) {
    CAppUI::setMsg($msg, UI_MSG_ERROR);
    return;
  }
}

if ($msg = $sejour_mutation->store()) {
  CAppUI::setMsg($msg, UI_MSG_ERROR);
  return;
}

if ($msg = $rpu->store()) {
  CAppUI::setMsg($msg, UI_MSG_ERROR);
  return;
}