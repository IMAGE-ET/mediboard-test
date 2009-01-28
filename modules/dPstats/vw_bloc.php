<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsEdit();

$filter = new COperation();

global $debutact, $finact, $prat_id, $salle_id, $discipline_id, $codes_ccam;

$debutact      = $filter->_date_min = mbGetValueFromGetOrSession("_date_min", mbDate("-1 YEAR"));
$rectif        = mbTransformTime("+0 DAY", $filter->_date_min, "%d")-1;
$debutact      = $filter->_date_min = mbDate("-$rectif DAYS", $filter->_date_min);

$finact        = $filter->_date_max = mbGetValueFromGetOrSession("_date_max",  mbDate());
$rectif        = mbTransformTime("+0 DAY", $filter->_date_max, "%d")-1;
$finact        = $filter->_date_max = mbDate("-$rectif DAYS", $filter->_date_max);
$finact        = $filter->_date_max = mbDate("+ 1 MONTH", $filter->_date_max);
$finact        = $filter->_date_max = mbDate("-1 DAY", $filter->_date_max);

$prat_id       = $filter->_prat_id = mbGetValueFromGetOrSession("prat_id", 0);
$salle_id      = $filter->salle_id = mbGetValueFromGetOrSession("salle_id", 0);
$bloc_id       = mbGetValueFromGet("bloc_id");
$discipline_id = $filter->_specialite = mbGetValueFromGetOrSession("discipline_id", 0);
$codes_ccam    = $filter->codes_ccam = strtoupper(mbGetValueFromGetOrSession("codes_ccam", ""));
$discipline_id = $filter->_specialite = mbGetValueFromGetOrSession("discipline_id", 0);

// map Graph Interventions
CAppUI::requireModuleFile($m, "inc_graph_activite");

global $graph, $options;
$graph->render("in",$options);
$map_graph_interventions = $graph->getHTMLImageMap();
$map_graph_interventions = preg_replace("/javascript:/", "#nothing\" onclick=\"", $map_graph_interventions);

$user = new CMediusers;
$listPrats = $user->loadPraticiens(PERM_READ);

$bloc = new CBlocOperatoire();
$listBlocs = CGroups::loadCurrent()->loadBlocs();

$listBlocsForSalles = $listBlocs;

if (!$bloc->load($bloc_id)) {
  $bloc = reset($listBlocs);
}
else {
	foreach ($listBlocsForSalles as $key => &$curr_bloc) {
		if ($curr_bloc->_id != $bloc->_id) {
			unset ($listBlocsForSalles[$key]);
		}
	}
}

$listDisciplines = new CDiscipline();
$listDisciplines = $listDisciplines->loadUsedDisciplines();

// Statistiques de horaires
$salle = new CSalle();
$ds = $salle->_spec->ds;
// Ce script ne fontionne pas pour une raison inconnue.
// Fonctionne en direct dans PMA
// $horaires = $ds->loadList(file_get_contents("modules/dPstats/sql/horaires_salles.sql"));


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("user_id"                 , $AppUI->user_id );
$smarty->assign("filter"       			  		, $filter         );
$smarty->assign("map_graph_interventions" , $map_graph_interventions);
$smarty->assign("listPrats"      		  		, $listPrats      );
$smarty->assign("listBlocs"               , $listBlocs      );
$smarty->assign("listBlocsForSalles"      , $listBlocsForSalles);
$smarty->assign("bloc"                    , $bloc           );
$smarty->assign("listDisciplines"		  		, $listDisciplines);

$smarty->display("vw_bloc.tpl");

?>