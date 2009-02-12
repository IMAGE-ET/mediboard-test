<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsRead();

$filter = new CSejour();

$filter->_date_min_stat = mbGetValueFromGetOrSession("_date_min_stat", mbDate("-1 YEAR"));
$rectif = mbTransformTime("+0 DAY", $filter->_date_min_stat, "%d")-1;
$filter->_date_min_stat = mbDate("-$rectif DAYS", $filter->_date_min_stat);

$filter->_date_max_stat = mbGetValueFromGetOrSession("_date_max_stat",  mbDate());
$rectif = mbTransformTime("+0 DAY", $filter->_date_max_stat, "%d")-1;
$filter->_date_max_stat = mbDate("-$rectif DAYS", $filter->_date_max_stat);
$filter->_date_max_stat = mbDate("+ 1 MONTH", $filter->_date_max_stat);
$filter->_date_max_stat = mbDate("-1 DAY", $filter->_date_max_stat);

$filter->_service = mbGetValueFromGetOrSession("service_id", 0);
$filter->type = mbGetValueFromGetOrSession("type", 1);
$filter->praticien_id = mbGetValueFromGetOrSession("prat_id", 0);
$filter->_specialite = mbGetValueFromGetOrSession("discipline_id", 0);

$user = new CMediusers;
$listPrats = $user->loadPraticiens(PERM_READ);

$listServices = new CService;
$listServices = $listServices->loadGroupList();

$listDisciplines = new CDiscipline();
$listDisciplines = $listDisciplines->loadUsedDisciplines();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("filter"       	 , $filter);
$smarty->assign("listPrats"      , $listPrats);
$smarty->assign("listServices"   , $listServices);
$smarty->assign("listDisciplines", $listDisciplines);

$smarty->display("vw_hospitalisation.tpl");

?>