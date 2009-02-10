<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPboard
* @version $Revision: 2260 $
* @author Romain Ollivier
*/

CAppUI::requireModuleFile("dPboard", "inc_board");
global $prat;
if (!$prat->_id) {
  return;
}

$stats = array(
  "vw_sejours_interventions",
  "vw_stats_consultations",
  "vw_prescripteurs",
);

$stat = mbGetValueFromPostOrSession("stat", "vw_sejours_interventions");

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

?>