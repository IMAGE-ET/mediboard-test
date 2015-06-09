<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPcabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

global $m;

// Permissions ?
//$module = CModule::getInstalled($m);
//$canModule = $module->canDo();
//$canModule->needsEdit();

$prat_id       = CValue::post("_prat_id");
$patient_id    = CValue::post("patient_id");
$_operation_id = CValue::post("_operation_id");
$_datetime     = CValue::post("_datetime");
$callback      = CValue::post("callback");
$type          = CValue::post("type");
$_in_suivi     = CValue::post("_in_suivi", 0);
$grossesse_id  = CValue::post("grossesse_id");
$uf_soins_id   = CValue::post("_uf_soins_id");

if (!$_datetime || $_datetime == "now") {
  $_datetime = CMbDT::dateTime();
}

$sejour = new CSejour();
$sejour->load(CValue::post("sejour_id"));

// Cas des urgences
if ($sejour->type === "urg" && !$_in_suivi) {
  if ($_datetime < $sejour->entree || $_datetime > $sejour->sortie) {
    CAppUI::setMsg("La prise en charge doit être dans les bornes du séjour", UI_MSG_ERROR);
    CAppUI::redirect("m=dPurgences");
  }
  
  $sejour->loadRefsConsultations();
  if ($sejour->_ref_consult_atu->_id) {
    CAppUI::setMsg("Patient déjà pris en charge par un praticien", UI_MSG_ERROR);
    CAppUI::redirect("m=dPurgences");
  }
  
  // Changement de praticien pour le sejour
  if (CAppUI::conf("dPurgences pec_change_prat")) {
    $sejour->praticien_id = $prat_id;
    if ($msg = $sejour->store()) {
      CAppUI::setMsg($msg, UI_MSG_ERROR);
      CAppUI::redirect("m=dPurgences");
    }
  }
}


$chir = new CMediusers;
$chir->load($prat_id);
if (!$chir->_id) {
  CAppUI::setMsg("Vous devez choisir un praticien pour la consultation", UI_MSG_ERROR);
}


$day_now  = CMbDT::format($_datetime, "%Y-%m-%d");
$time_now = CMbDT::format($_datetime, "%H:%M:00");
$hour_now = CMbDT::format($_datetime, "%H:00:00");
$hour_next = CMbDT::time("+1 HOUR", $hour_now);

$plage = new CPlageconsult();
$plageBefore = new CPlageconsult();
$plageAfter = new CPlageconsult();

// Cas ou une plage correspond
$where = array();
$where["chir_id"] = "= '$chir->_id'";
$where["date"]    = "= '$day_now'";
$where["debut"]   = "<= '$time_now'";
$where["fin"]     = "> '$time_now'";
$plage->loadObject($where);

if (!$plage->_id) {
  // Cas ou on a des plage en collision
  $where = array();
  $where["chir_id"] = "= '$chir->_id'";
  $where["date"]    = "= '$day_now'";
  $where["debut"]   = "<= '$hour_now'";
  $where["fin"]     = ">= '$hour_now'";
  $plageBefore->loadObject($where);
  $where["debut"]   = "<= '$hour_next'";
  $where["fin"]     = ">= '$time_now'";
  $plageAfter->loadObject($where);
  if ($plageBefore->_id) {
    if ($plageAfter->_id) {
      $plageBefore->fin = $plageAfter->debut;
    }
    else {
      $plageBefore->fin = max($plageBefore->fin, $hour_next);
    }
    $plage =& $plageBefore;
  }
  elseif ($plageAfter->_id) {
    $plageAfter->debut = min($plageAfter->debut, $hour_now);
    $plage =& $plageAfter;
  }
  else {
    $plage->chir_id = $chir->_id;
    $plage->date    = $day_now;
    $plage->freq    = "00:".CPlageconsult::$minutes_interval.":00";
    $plage->debut   = $hour_now;
    $plage->fin     = $hour_next;
    $plage->libelle = "automatique";
    $plage->_immediate_plage = 1;
  }
  $plage->updateFormFields();
  if ($msg = $plage->store()) {
    CAppUI::setMsg($msg, UI_MSG_ERROR);
  }
}

$plage->loadRefsFwd();

if ($grossesse_id) {
  $grossesse = new CGrossesse();
  $grossesse->load($grossesse_id);
  if ($grossesse->_id) {
    $patient_id = $grossesse->parturiente_id;
  }
}

$ref_chir = $plage->_ref_chir;

$consult = new CConsultation;
$consult->plageconsult_id = $plage->_id;
$consult->sejour_id       = $sejour->_id;
$consult->grossesse_id    = $grossesse_id;
$consult->patient_id      = $patient_id;
$consult->heure           = $time_now;
$consult->arrivee         = "$day_now $time_now";
$consult->duree           = 1;
$consult->chrono          = CConsultation::PATIENT_ARRIVE;
$consult->date_at         = CValue::post("date_at");
$consult->_operation_id   = $_operation_id;
$consult->_uf_soins_id    = $uf_soins_id;

if ($type) {
  $consult->type = $type;
}

// Cas standard
$consult->motif = CValue::post(
  "motif", CAppUI::conf('dPcabinet CConsultation show_motif_consult_immediate') ? "Consultation immédiate" : null
);

if ($type == "entree") {
  $consult->motif = CAppUI::conf('soins Other default_motif_observation', CGroups::loadCurrent()->_guid);
}
// Cas des urgences
if ($sejour->type == "urg") {
  // Motif de la consultation
  $consult->motif = "";
  if (CAppUI::conf('dPurgences motif_rpu_view')) {
    $consult->motif .= "RPU: ";

    $sejour->loadRefRPU();
    $consult->motif.= $sejour->_ref_rpu->diag_infirmier;
  }
} 

if ($msg = $consult->store()) {
  CAppUI::setMsg($msg, UI_MSG_ERROR);
}

CAppUI::setMsg("CConsultation-msg-create", UI_MSG_OK);


// Redirect final
if ($ajax) {
  echo CAppUI::getMsg();
  if ($callback) {
    CAppUI::callbackAjax($callback, $consult->_id);
  }
  CApp::rip();
}

if ($current_m = CValue::post("_m_redirect")) {
  CAppUI::redirect("m=$current_m");
}
else {
  $current_m = ($sejour->type == "urg") ? "dPurgences" : "dPcabinet";
  CAppUI::redirect("m=$current_m&tab=edit_consultation&selConsult=$consult->consultation_id&chirSel=$chir->user_id");
}
