<?php /* $Id: */

/**
* @package Mediboard
* @subpackage dPbloc
* @version $Revision$
* @author Romain Ollivier
*/
 
global $AppUI, $can;
$can->needsRead();

$now = mbDate();

$filter = new COperation;
$filter->_date_min     = mbGetValueFromGet("_date_min", $now);
$filter->_date_max     = mbGetValueFromGet("_date_max", $now);
$filter->_prat_id      = mbGetValueFromGetOrSession("_prat_id");
$filter->salle_id      = mbGetValueFromGetOrSession("salle_id");
$filter->_plage        = mbGetValueFromGetOrSession("_plage");
$filter->_intervention = mbGetValueFromGetOrSession("_intervention");
$filter->_specialite   = mbGetValueFromGetOrSession("_specialite");
$filter->_codes_ccam   = mbGetValueFromGetOrSession("_codes_ccam");
$filter->_ccam_libelle = mbGetValueFromGetOrSession("_ccam_libelle");

$filterSejour = new CSejour;
$filterSejour->type = mbGetValueFromGetOrSession("type");

$tomorrow  = mbDate("+1 day", $now);

$week_deb  = mbDate("last sunday", $now);
$week_fin  = mbDate("next sunday", $week_deb);
$week_deb  = mbDate("+1 day"     , $week_deb);

$rectif     = mbTransformTime("+0 DAY", $now, "%d")-1;
$month_deb  = mbDate("-$rectif DAYS", $now);
$month_fin  = mbDate("+1 month", $month_deb);
$month_fin  = mbDate("-1 day", $month_fin);

$listPrat = new CMediusers();
$listPrat = $listPrat->loadPraticiens(PERM_READ);

$listSpec = new CFunctions();
$listSpec = $listSpec->loadSpecialites(PERM_READ);

$order = "nom";
$salle = new CSalle();
$listSalles = $salle->loadListWithPerms(PERM_READ, null, $order);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("chir"         , $AppUI->user_id);
$smarty->assign("filter"       , $filter);
$smarty->assign("filterSejour" , $filterSejour);
$smarty->assign("now"          , $now);
$smarty->assign("tomorrow"     , $tomorrow);
$smarty->assign("week_deb"     , $week_deb);
$smarty->assign("week_fin"     , $week_fin);
$smarty->assign("month_deb"    , $month_deb);
$smarty->assign("month_fin"    , $month_fin);
$smarty->assign("listPrat"     , $listPrat);
$smarty->assign("listSpec"     , $listSpec);
$smarty->assign("listSalles"   , $listSalles);

$smarty->display("print_planning.tpl");

?>