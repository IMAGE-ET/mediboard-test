<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstats
 * @version $Revision$
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

// map Graph Interventions
CAppUI::requireModuleFile("dPstats", "graph_activite_zoom");

$graphs = array(
  graphActiviteZoom($date, $prat_id, $salle_id, $bloc_id, $discipline_id, $codes_ccam),
);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("graphs", $graphs);
$smarty->display("vw_graph_activite_zoom.tpl");

?>