<?php /* $Id: */

/**
 * @package Mediboard
 * @subpackage dPstats
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

$date          = CValue::getOrSession("date", CMbDT::transform("+0 DAY", CMbDT::date(), "%m/%Y"));
$prat_id       = CValue::getOrSession("prat_id", 0);
$salle_id      = CValue::getOrSession("salle_id", 0);
$bloc_id       = CValue::getOrSession("bloc_id");
$discipline_id = CValue::getOrSession("discipline_id", 0);
$codes_ccam    = strtoupper(CValue::getOrSession("codes_ccam", ""));
$hors_plage    = CValue::getOrSession("hors_plage", 1);
$type_hospi    = CValue::getOrSession("type_hospi", '');
$debut = substr($date, 3, 7)."-".substr($date, 0, 2)."-01";
$fin = CMbDT::date("+1 MONTH", $debut);
$fin = CMbDT::date("-1 DAY", $fin);

// map Graph occupation salle
CAppUI::requireModuleFile("dPstats", "graph_occupation_salle");

$graphs = array(
  graphOccupationSalle($debut, $fin, $prat_id, $salle_id, $bloc_id, $discipline_id, $codes_ccam, $type_hospi, $hors_plage, 'DAY')
);

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("graphs", $graphs);
$smarty->display("vw_graph_activite_zoom.tpl");
