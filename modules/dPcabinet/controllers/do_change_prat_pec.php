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

$prat_id = CValue::post("prat_id");
$consult = new CConsultation;
$consult->load(CValue::post("consultation_id"));
$consult->loadRefPlageConsult();
$_datetime = $consult->_datetime;

if (!isset($current_m)) {
  $current_m = CValue::post("current_m", "dPcabinet");
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
$where["chir_id"] = "= '$prat_id'";
$where["date"]    = "= '$day_now'";
$where["debut"]   = "<= '$time_now'";
$where["fin"]     = "> '$time_now'";
$plage->loadObject($where);

if (!$plage->_id) {
  // Cas ou on a des plage en collision
  $where = array();
  $where["chir_id"] = "= '$prat_id'";
  $where["date"]    = "= '$day_now'";
  $where["debut"]   = "<= '$hour_now'";
  $where["fin"]     = ">= '$hour_now'";
  $plageBefore->loadObject($where);
  $where["debut"]   = "<= '$hour_next'";
  $where["fin"]     = ">= '$hour_next'";
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
    $plage->chir_id = $prat_id;
    $plage->date    = $day_now;
    $plage->freq    = "00:".CPlageconsult::$minutes_interval.":00";
    $plage->debut   = $hour_now;
    $plage->fin     = $hour_next;
    $plage->libelle = "automatique";
  }
  $plage->updateFormFields();
  if ($msg = $plage->store()) {
    CAppUI::setMsg($msg, UI_MSG_ERROR);
  }
}

$consult->plageconsult_id = $plage->_id;
if ($msg = $consult->store()) {
  CAppUI::setMsg($msg, UI_MSG_ERROR);
}

if ($current_m == "dPurgences") {
  CAppUI::redirect("m=dPurgences&tab=edit_consultation&selConsult=$consult->_id&ajax=$ajax");
}
else {
  CAppUI::redirect("m=dPcabinet&tab=edit_consultation&selConsult=$consult->_id&ajax=$ajax");
}
