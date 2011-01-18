<?php /* $Id: vw_graph_activite_zoom.php 7211 2009-11-03 12:27:08Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage dPstats
 * @version $Revision: 7211 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can, $m;
$can->needsEdit();

$date          = CValue::getOrSession("date", mbTransformTime("+0 DAY", mbDate(), "%m/%Y"));
$prat_id       = CValue::getOrSession("prat_id", 0);
$salle_id      = CValue::getOrSession("salle_id", 0);
$bloc_id       = CValue::getOrSession("bloc_id");
$discipline_id = CValue::getOrSession("discipline_id", 0);
$codes_ccam    = strtoupper(CValue::getOrSession("codes_ccam", ""));

CAppUI::requireModuleFile("dPstats", "graph_occupation_salle_zoom");

$debut = substr($date,3,7)."-".substr($date,0,2)."-01";
$fin   = mbDate("+1 MONTH", $debut);
$fin   = mbDate("-1 DAY", $fin);

$graphs = array(
  graphOccupationSalle($debut, $fin, $prat_id, $salle_id, $bloc_id, $discipline_id, $codes_ccam),
);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("graphs", $graphs);
$smarty->display("vw_graph_activite_zoom.tpl");

?>