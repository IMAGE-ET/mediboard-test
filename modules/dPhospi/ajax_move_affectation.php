<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Hospi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkEdit();

$affectation_id = CValue::get("affectation_id");
$lit_id         = CValue::get("lit_id");
$sejour_id      = CValue::get("sejour_id");
$service_id     = CValue::get("service_id");

$affectation = new CAffectation();

if ($affectation_id) {
  $affectation->load($affectation_id);
  
  // On déplace l'affectation parente si nécessaire
  if (null != $affectation_id = $affectation->parent_affectation_id) {
    $affectation = new CAffectation();
    $affectation->load($affectation_id);
  }
}
else {
  $affectation->sejour_id = $sejour_id;
  $sejour = new CSejour();
  $sejour->load($sejour_id);
  $affectation->entree = $sejour->entree;
  $affectation->sortie = $sejour->sortie;
}

// Couloir
if ($service_id) {
  $affectation->service_id = $service_id;
}
// Changement de lit
else {
  $affectation->lit_id = $lit_id;
}

// Si l'affectation est un blocage, il faut vider le champ sejour_id
if ($affectation->sejour_id == 0) {
  $affectation->sejour_id = "";
}

if ($msg = $affectation->store()) {
  CAppUI::setMsg($msg, UI_MSG_ERROR);
}

$affectations_enfant = $affectation->loadBackRefs("affectations_enfant");
foreach ($affectations_enfant as $_affectation) {
  $_affectation->lit_id = $lit_id;
  if ($msg = $_affectation->store()) {
    CAppUI::setMsg($msg, UI_MSG_ERROR);
  }
}

echo CAppUI::getMsg();
