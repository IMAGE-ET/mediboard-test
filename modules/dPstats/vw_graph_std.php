<?php
/**
 * $Id: $
 *
 * @package    Mediboard
 * @subpackage dPstats
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: $
 */

CCanDo::checkRead();

// map Graph Hospi
CAppUI::requireModuleFile('dPstats', 'graph_patparservice');
CAppUI::requireModuleFile('dPstats', 'graph_patpartypehospi');
CAppUI::requireModuleFile('dPstats', 'graph_joursparservice');

// map Graph Bloc
CAppUI::requireModuleFile("dPstats", "graph_activite");
CAppUI::requireModuleFile("dPstats", "graph_activite_zoom");
CAppUI::requireModuleFile("dPstats", "graph_pratdiscipline");
CAppUI::requireModuleFile("dPstats", "graph_patjoursalle");
CAppUI::requireModuleFile("dPstats", "graph_op_annulees");
CAppUI::requireModuleFile("dPstats", "graph_occupation_salle_total");
CAppUI::requireModuleFile("dPstats", "graph_temps_salle");

// map Graph SSPI
CAppUI::requireModuleFile("dPstats", "graph_patparheure_reveil");
CAppUI::requireModuleFile("dPstats", "graph_patrepartjour");

// Bornes de date des statistiques
$_date_min = CValue::get("_date_min", CMbDT::date("-1 YEAR"));
$rectif    = CMbDT::transform("+0 DAY", $_date_min, "%d")-1;
$_date_min = CMbDT::date("-$rectif DAYS", $_date_min);

$_date_max = CValue::get("_date_max",  CMbDT::date());
$rectif    = CMbDT::transform("+0 DAY", $_date_max, "%d")-1;
$_date_max = CMbDT::date("-$rectif DAYS", $_date_max);
$_date_max = CMbDT::date("+ 1 MONTH", $_date_max);
$_date_max = CMbDT::date("-1 DAY", $_date_max);

$date_zoom = CValue::get("date_zoom", CMbDT::transform("+0 DAY", CMbDT::date(), "%m/%Y"));
$debut_zoom = substr($date_zoom, 3, 7)."-".substr($date_zoom, 0, 2)."-01";
$fin_zoom = CMbDT::date("+1 MONTH", $debut_zoom);
$fin_zoom = CMbDT::date("-1 DAY", $fin_zoom);

// Autres éléments de filtrage
$service_id    = CValue::get("service_id", 0);
$type          = CValue::get("type", 1);
$prat_id       = CValue::get("prat_id", 0);
$discipline_id = CValue::get("discipline_id", 0);
$septique      = CValue::get("septique", 0);
$type_data     = CValue::get("type_data", "prevue");

$salle_id      = CValue::get("salle_id", 0);
$bloc_id       = CValue::get("bloc_id");
$codes_ccam    = strtoupper(CValue::get("codes_ccam", ""));
$hors_plage    = CValue::get("hors_plage", 1);

// Possibilité de zoom
$can_zoom  = CValue::get("can_zoom");

// Nom du graphique à afficher
$type_graph = CValue::get("type_graph");

switch ($type_graph) {
  case "patparservice":
    $graph = graphPatParService(
      $_date_min, $_date_max,
      $prat_id, $service_id, $type,
      $discipline_id, $septique, $type_data
    );
    break;
  case "patpartypehospi":
    $graph = graphPatParTypeHospi(
      $_date_min, $_date_max,
      $prat_id, $service_id, $type,
      $discipline_id, $septique, $type_data
    );
    break;
  case "jourparservice":
    $graph = graphJoursParService(
      $_date_min, $_date_max,
      $prat_id, $service_id, $type,
      $discipline_id, $septique, $type_data
    );
    break;
  case "intervparsalle":
    $can_zoom = "intervparsallezoom";
    $graph = graphActivite(
      $_date_min, $_date_max,
      $prat_id, $salle_id, $bloc_id,
      $discipline_id, $codes_ccam, $type,
      $hors_plage
    );
    break;
  case "intervparsallezoom":
    $graph = graphActiviteZoom(
      $date_zoom,
      $prat_id, $salle_id, $bloc_id,
      $discipline_id, $codes_ccam, $type,
      $hors_plage
    );
    break;
  case "opannulees":
    $graph = graphOpAnnulees(
      $_date_min, $_date_max,
      $prat_id, $salle_id, $bloc_id,
      $codes_ccam, $type, $hors_plage
    );
    break;
  case "intervparprat":
    $graph = graphPraticienDiscipline(
      $_date_min, $_date_max,
      $prat_id, $salle_id, $bloc_id,
      $discipline_id, $codes_ccam, $type,
      $hors_plage
    );
    break;
  case "occupationsalletotal":
    $can_zoom = "occupationsalletotalzoom";
    $listOccupation = graphOccupationSalle(
      $_date_min, $_date_max,
      $prat_id, $salle_id, $bloc_id,
      $discipline_id, $codes_ccam, $type,
      $hors_plage, 'MONTH'
    );
    $graph = $listOccupation["total"];
    break;
  case "occupationsalletotalzoom":
    $graph = graphOccupationSalle(
      $debut_zoom, $fin_zoom,
      $prat_id, $salle_id, $bloc_id,
      $discipline_id, $codes_ccam, $type,
      $hors_plage, 'DAY'
    );
    break;
  case "occupationsallemoy":
    $listOccupation = graphOccupationSalle(
      $_date_min, $_date_max,
      $prat_id, $salle_id, $bloc_id,
      $discipline_id, $codes_ccam, $type,
      $hors_plage, 'MONTH'
    );
    $graph = $listOccupation["moyenne"];
    break;
  case "ressourcesbloc":
    $can_zoom = "ressourcesbloczoom";
    $graph = graphTempsSalle(
      $_date_min, $_date_max,
      $prat_id, $salle_id, $bloc_id,
      $discipline_id, $codes_ccam, $type,
      $hors_plage, 'MONTH'
    );
    break;
  case "ressourcesbloczoom":
    $graph = graphTempsSalle(
      $debut_zoom, $fin_zoom,
      $prat_id, $salle_id, $bloc_id,
      $discipline_id, $codes_ccam, $type,
      $hors_plage, 'DAY'
    );
    break;
  case "patjoursalle":
    $graph = graphPatJourSalle(
      $_date_min, $_date_max,
      $prat_id, $salle_id, $bloc_id,
      $discipline_id, $codes_ccam, $hors_plage
    );
    break;
  case "patparjoursspi":
    $graph = graphPatRepartJour(
      $_date_min, $_date_max,
      $prat_id, $bloc_id, $discipline_id,
      $codes_ccam
    );
    break;
  case "patparheuresspi":
    $graph = graphPatParHeureReveil(
      $_date_min, $_date_max,
      $prat_id, $bloc_id, $discipline_id,
      $codes_ccam
    );
    break;
  default:
    $graph = array();
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("can_zoom"  , $can_zoom);
$smarty->assign("type_graph", $type_graph);
$smarty->assign("graph"     , $graph);

$smarty->display("vw_graph_std.tpl");