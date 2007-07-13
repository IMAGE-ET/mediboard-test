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

$debutact = $filter->_date_min = mbGetValueFromGetOrSession("_date_min", mbDate("-1 YEAR"));
$rectif = mbTranformTime("+0 DAY", $filter->_date_min, "%d")-1;
$debutact = $filter->_date_min = mbDate("-$rectif DAYS", $filter->_date_min);

$finact = $filter->_date_max = mbGetValueFromGetOrSession("_date_max",  mbDate());
$rectif = mbTranformTime("+0 DAY", $filter->_date_max, "%d")-1;
$finact = $filter->_date_max = mbDate("-$rectif DAYS", $filter->_date_max);
$finact = $filter->_date_max = mbDate("+ 1 MONTH", $filter->_date_max);
$finact = $filter->_date_max = mbDate("-1 DAY", $filter->_date_max);

$salle_id = $filter->salle_id = mbGetValueFromGetOrSession("salle_id", 0);
$codes_ccam = $filter->codes_ccam = strtoupper(mbGetValueFromGetOrSession("codes_ccam", ""));
$prat_id = $filter->_prat_id = mbGetValueFromGetOrSession("prat_id", 0);
$discipline_id = $filter->_specialite = mbGetValueFromGetOrSession("discipline_id", 0);

// map Graph Interventions
require_once($AppUI->getModuleFile($m, "inc_graph_activite"));

$graph->render("in",$options);
$map_graph_interventions = $graph->getHTMLImageMap();
$map_graph_interventions = preg_replace("/javascript:/", "#nothing\" onclick=\"", $map_graph_interventions);

$user = new CMediusers;
$listPrats = $user->loadPraticiens(PERM_READ);

$listSalles = new CSalle;
$where["stats"] = "= '1'";
$order = "nom";
$listSalles = $listSalles->loadList($where, $order);

$listDisciplines = new CDiscipline();
$listDisciplines = $listDisciplines->loadUsedDisciplines();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("user_id"                 , $AppUI->user_id);
$smarty->assign("filter"       			  		, $filter       );
$smarty->assign("map_graph_interventions" , $map_graph_interventions);
$smarty->assign("listPrats"      		  		, $listPrats      );
$smarty->assign("listSalles"     		  		, $listSalles     );
$smarty->assign("listDisciplines"		  		, $listDisciplines);

$smarty->display("vw_bloc.tpl");

?>