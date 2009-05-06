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

$date          = mbGetValueFromGetOrSession("date", mbTransformTime("+0 DAY", mbDate(), "%m/%Y"));
$prat_id       = mbGetValueFromGetOrSession("prat_id", 0);
$salle_id      = mbGetValueFromGetOrSession("salle_id", 0);
$bloc_id       = mbGetValueFromGetOrSession("bloc_id");
$discipline_id = mbGetValueFromGetOrSession("discipline_id", 0);
$codes_ccam    = strtoupper(mbGetValueFromGetOrSession("codes_ccam", ""));

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