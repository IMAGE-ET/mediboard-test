<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstats
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can, $m;

$can->needsRead();

CAppUI::requireModuleFile('dPstats', 'graph_patparservice');
CAppUI::requireModuleFile('dPstats', 'graph_patpartypehospi');
CAppUI::requireModuleFile('dPstats', 'graph_joursparservice');

$filter = new CSejour();

$filter->_date_min_stat = CValue::getOrSession("_date_min_stat", mbDate("-1 YEAR"));
$rectif = mbTransformTime("+0 DAY", $filter->_date_min_stat, "%d")-1;
$filter->_date_min_stat = mbDate("-$rectif DAYS", $filter->_date_min_stat);

$filter->_date_max_stat = CValue::getOrSession("_date_max_stat",  mbDate());
$rectif = mbTransformTime("+0 DAY", $filter->_date_max_stat, "%d")-1;
$filter->_date_max_stat = mbDate("-$rectif DAYS", $filter->_date_max_stat);
$filter->_date_max_stat = mbDate("+ 1 MONTH", $filter->_date_max_stat);
$filter->_date_max_stat = mbDate("-1 DAY", $filter->_date_max_stat);

$filter->_service = CValue::getOrSession("service_id", 0);
$filter->type = CValue::getOrSession("type", 1);
$filter->praticien_id = CValue::getOrSession("prat_id", 0);
$filter->_specialite = CValue::getOrSession("discipline_id", 0);

$type_data = CValue::getOrSession("type_data", "prevue");

// Qualité de l'information
$qualite = array();

// Liste des séjours totaux
$query = "SELECT COUNT(sejour.sejour_id) AS total, 1 as group_field
	    FROM sejour
	    INNER JOIN users_mediboard ON sejour.praticien_id = users_mediboard.user_id
	    LEFT JOIN affectation ON sejour.sejour_id = affectation.sejour_id
	    LEFT JOIN lit ON affectation.lit_id = lit.lit_id
	    LEFT JOIN chambre ON lit.chambre_id = chambre.chambre_id
	    LEFT JOIN service ON chambre.service_id = service.service_id
	    WHERE
			  sejour.entree_prevue BETWEEN '$filter->_date_min_stat 00:00:00' AND '$filter->_date_max_stat 23:59:59' AND
				sejour.group_id = '".CGroups::loadCurrent()->_id."' AND
				sejour.annule = '0'";
if($filter->_service)     $query .= "\nAND service.service_id = '$filter->_service'";
if($filter->praticien_id) $query .= "\nAND sejour.praticien_id = '$filter->praticien_id'";
if($filter->_specialite)  $query .= "\nAND users_mediboard.discipline_id = '$filter->_specialite'";
if($filter->type) {
  if($filter->type == 1)
    $query .= "\nAND (sejour.type = 'comp' OR sejour.type = 'ambu')";
  else
    $query .= "\nAND sejour.type = '$filter->type'";
}
$query .= "\nGROUP BY group_field";
$sejour = new CSejour;
$result = $sejour->_spec->ds->loadlist($query);

$qualite["total"] = 0;
if (count($result))
  $qualite["total"] = $result[0]["total"];

// 1. Patients placés
$query = "SELECT COUNT(sejour.sejour_id) AS total, 1 as group_field
	    FROM sejour
	    INNER JOIN users_mediboard ON sejour.praticien_id = users_mediboard.user_id
	    LEFT JOIN affectation ON sejour.sejour_id = affectation.sejour_id
	    LEFT JOIN lit ON affectation.lit_id = lit.lit_id
	    LEFT JOIN chambre ON lit.chambre_id = chambre.chambre_id
	    LEFT JOIN service ON chambre.service_id = service.service_id
	    WHERE
			  sejour.entree_prevue BETWEEN '$filter->_date_min_stat 00:00:00' AND '$filter->_date_max_stat 23:59:59' AND
				sejour.group_id = '".CGroups::loadCurrent()->_id."' AND
				sejour.annule = '0' AND
        affectation.affectation_id IS NOT NULL";
if($filter->_service)     $query .= "\nAND service.service_id = '$filter->_service'";
if($filter->praticien_id) $query .= "\nAND sejour.praticien_id = '$filter->praticien_id'";
if($filter->_specialite)  $query .= "\nAND users_mediboard.discipline_id = '$filter->_specialite'";
if($filter->type) {
  if($filter->type == 1)
    $query .= "\nAND (sejour.type = 'comp' OR sejour.type = 'ambu')";
  else
    $query .= "\nAND sejour.type = '$filter->type'";
}
$query .= "\nGROUP BY group_field";
$sejour = new CSejour;
$result = $sejour->_spec->ds->loadlist($query);

$qualite["places"]["total"] = 0;
$qualite["places"]["pct"]   = 0;

if (count($result)) {
  $qualite["places"]["total"] = $result[0]["total"];
  $qualite["places"]["pct"]   = $result[0]["total"] / $qualite["total"] * 100;
}

// 2. Séjours sans entrées ou sorties réelles
$query = "SELECT COUNT(sejour.sejour_id) AS total, 1 as group_field
	    FROM sejour
	    INNER JOIN users_mediboard ON sejour.praticien_id = users_mediboard.user_id
	    LEFT JOIN affectation ON sejour.sejour_id = affectation.sejour_id
	    LEFT JOIN lit ON affectation.lit_id = lit.lit_id
	    LEFT JOIN chambre ON lit.chambre_id = chambre.chambre_id
	    LEFT JOIN service ON chambre.service_id = service.service_id
	    WHERE
			  sejour.entree_prevue BETWEEN '$filter->_date_min_stat 00:00:00' AND '$filter->_date_max_stat 23:59:59' AND
				sejour.group_id = '".CGroups::loadCurrent()->_id."' AND
				sejour.annule = '0' AND
        sejour.entree_reelle IS NOT NULL AND
        sejour.sortie_reelle IS NOT NULL";
if($filter->_service)     $query .= "\nAND service.service_id = '$filter->_service'";
if($filter->praticien_id) $query .= "\nAND sejour.praticien_id = '$filter->praticien_id'";
if($filter->_specialite)  $query .= "\nAND users_mediboard.discipline_id = '$filter->_specialite'";
if($filter->type) {
  if($filter->type == 1)
    $query .= "\nAND (sejour.type = 'comp' OR sejour.type = 'ambu')";
  else
    $query .= "\nAND sejour.type = '$filter->type'";
}
$query .= "\nGROUP BY group_field";
$sejour = new CSejour;
$result = $sejour->_spec->ds->loadlist($query);

$qualite["reels"]["total"] = 0;
$qualite["reels"]["pct"]   = 0;
  
if (count($result)) {
  $qualite["reels"]["total"] = $result[0]["total"];
  $qualite["reels"]["pct"]   = $result[0]["total"] / $qualite["total"] * 100;
}

$user = new CMediusers;
$listPrats = $user->loadPraticiens(PERM_READ);

$listServices = new CService;
$listServices = $listServices->loadGroupList();

$listDisciplines = new CDiscipline();
$listDisciplines = $listDisciplines->loadUsedDisciplines();

$graphs = array(
  graphPatParService($filter->_date_min_stat, $filter->_date_max_stat, $filter->praticien_id, $filter->_service, $filter->type, $filter->_specialite, $type_data),
	graphPatParTypeHospi($filter->_date_min_stat, $filter->_date_max_stat, $filter->praticien_id, $filter->_service, $filter->type, $filter->_specialite, $type_data),
	graphJoursParService($filter->_date_min_stat, $filter->_date_max_stat, $filter->praticien_id, $filter->_service, $filter->type, $filter->_specialite, $type_data)
);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("filter"       	 , $filter);
$smarty->assign("type_data"      , $type_data);
$smarty->assign("listPrats"      , $listPrats);
$smarty->assign("listServices"   , $listServices);
$smarty->assign("listDisciplines", $listDisciplines);
$smarty->assign("qualite"        , $qualite);
$smarty->assign("graphs"         , $graphs);

$smarty->display("vw_hospitalisation.tpl");

?>