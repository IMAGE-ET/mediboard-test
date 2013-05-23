<?php

/**
 * dPboard
 *
 * @category Board
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

CAppUI::requireModuleFile("dPboard", "inc_board");
global $prat;
if (!$prat->_id) {
  return;
}

$stats = array(
  "vw_sejours_interventions",
  "vw_stats_consultations",
  "vw_prescripteurs"
);

if (CModule::getActive("dPprescription")) {
  $stats[] = "vw_stats_prescriptions";
}

if (CAppUI::conf("dPplanningOp COperation verif_cote")) {
  $stats[] = "vw_trace_cotes";
}

$stat = CValue::postOrSession("stat", "vw_sejours_interventions");

if (!in_array($stat, $stats)) {
  trigger_error("Unknown stat view '$stat'", E_USER_WARNING);
  return;
}

// Affichage
$smarty = new CSmartyDP();

$smarty->assign("stats", $stats);
$smarty->assign("stat" , $stat);

$smarty->display("vw_stats.tpl");

CAppUI::requireModuleFile("dPboard", "$stat");
