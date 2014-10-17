<?php 

/**
 * $Id$
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

$event    = CValue::post("event");
$callback = CValue::post("callback");

switch ($event) {
  case "A01":
    $sejour = new CSejour();
    $sejour->sortie_prevue = CValue::post("sortie_prevue");
    $sejour->entree_prevue = CValue::post("entree_prevue");
    $sejour->entree_reelle = CValue::post("entree_reelle");
    $sejour->patient_id    = CValue::post("patient_id");
    $sejour->group_id      = CValue::post("group_id");
    $sejour->type          = CValue::post("type");
    $sejour->praticien_id  = CValue::post("praticien_id");

    if ($msg = $sejour->store()) {
      CAppUI::stepAjax($msg, UI_MSG_ERROR);
    }
    break;
  case "A02":

    break;
  case "A03":
    $sejour_id = CValue::post("sejour_id");
    $sejour = new CSejour();
    $sejour->load($sejour_id);
    $sejour->sortie_reelle = CValue::post("sortie_reelle");

    if ($msg = $sejour->store()) {
      CAppUI::stepAjax($msg, UI_MSG_ERROR);
    }
    break;
  case "A04":
    $sejour = new CSejour();
    $sejour->sortie_prevue = CValue::post("sortie_prevue");
    $sejour->entree_prevue = CValue::post("entree_prevue");
    $sejour->entree_reelle = CValue::post("entree_reelle");
    $sejour->patient_id    = CValue::post("patient_id");
    $sejour->group_id      = CValue::post("group_id");
    $sejour->type          = CValue::post("type");
    $sejour->praticien_id  = CValue::post("praticien_id");

    if ($msg = $sejour->store()) {
      CAppUI::stepAjax($msg, UI_MSG_ERROR);
    }
    break;
  case "A05":
    $sejour = new CSejour();
    $sejour->sortie_prevue = CValue::post("sortie_prevue");
    $sejour->entree_prevue = CValue::post("entree_prevue");
    $sejour->patient_id    = CValue::post("patient_id");
    $sejour->group_id      = CValue::post("group_id");
    $sejour->type          = CValue::post("type");
    $sejour->praticien_id  = CValue::post("praticien_id");

    if ($msg = $sejour->store()) {
      CAppUI::stepAjax($msg, UI_MSG_ERROR);
    }
    break;
  case "A11":
    $sejour_id = CValue::post("sejour_id");
    $sejour = new CSejour();
    $sejour->load($sejour_id);
    $sejour->annule = "1";

    if ($msg = $sejour->store()) {
      CAppUI::stepAjax($msg, UI_MSG_ERROR);
    }
    break;
  case "A12":
    $affectation_id = CValue::post("affectation_id");
    $affectation = new CAffectation();
    $affectation->load($affectation_id);
    if ($msg = $affectation->delete()) {
      CAppUI::stepAjax($msg, UI_MSG_ERROR);
    }
    break;
  case "A13":
    $sejour_id = CValue::post("sejour_id");
    $sejour = new CSejour();
    $sejour->load($sejour_id);
    $sejour->sortie_reelle = "";

    if ($msg = $sejour->store()) {
      CAppUI::stepAjax($msg, UI_MSG_ERROR);
    }
    break;
  case "INSERT":
    $sejour_id = CValue::post("sejour_id");
    $sejour = new CSejour();
    $sejour->load($sejour_id);
    $sejour->type = CValue::post("type");

    if ($msg = $sejour->store()) {
      CAppUI::stepAjax($msg, UI_MSG_ERROR);
    }
    break;
  default:
    CAppUI::stepAjax("L'évenement choisit n'est pas supporté", UI_MSG_ERROR);
}

CAppUI::stepAjax("Evenement effectué");

if ($callback) {
  CAppUI::callbackAjax($callback);
}

CApp::rip();