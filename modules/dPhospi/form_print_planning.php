<?php /* $Id$*/

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Romain Ollivier
*/
 
global $AppUI, $can, $m, $g;

$can->needsRead();

$filter = new CSejour();
$today      = mbDate();
$filter->_date_min     = mbGetValueFromGetOrSession("_date_min","$today 06:00:00");
$filter->_date_max     = mbGetValueFromGetOrSession("_date_max","$today 21:00:00");
$filter->_horodatage   = mbGetValueFromGetOrSession("_horodatage", "entree_prevue");
$filter->_admission    = mbGetValueFromGetOrSession("_admission");
$filter->_service      = mbGetValueFromGetOrSession("_service");
$filter->praticien_id  = mbGetValueFromGetOrSession("praticien_id");
$filter->convalescence = mbGetValueFromGetOrSession("convalescence");
$filter->_specialite   = mbGetValueFromGetOrSession("_specialite");
$filter->_filter_type  = mbGetValueFromGetOrSession("_filter_type");
$filter->_ccam_libelle = mbGetValueFromGetOrSession("_ccam_libelle", 1);
$filter->_coordonnees  = mbGetValueFromGetOrSession("_coordonnees");

$listPrat = new CMediusers();
$listPrat = $listPrat->loadPraticiens(PERM_READ);

$listSpec = new CFunctions();
$listSpec = $listSpec->loadSpecialites(PERM_READ);

$listServ = new CService();
$where = array();
$where["group_id"] = "= '$g'";
$listServ = $listServ->loadListWithPerms(PERM_READ,$where);

$yesterday  = mbDate("-1 day", $today);
$tomorrow   = mbDate("+1 day", $today);

$yesterday_deb  = "$yesterday 06:00:00";
$yesterday_fin  = "$yesterday 21:00:00";
$today_deb      = "$today 06:00:00";
$today_fin      = "$today 21:00:00";
$tomorrow_deb   = "$tomorrow 06:00:00";
$tomorrow_fin   = "$tomorrow 21:00:00";

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("yesterday_deb"    , $yesterday_deb);
$smarty->assign("yesterday_fin"    , $yesterday_fin);
$smarty->assign("today_deb"    , $today_deb);
$smarty->assign("today_fin"    , $today_fin);
$smarty->assign("tomorrow_deb" , $tomorrow_deb);
$smarty->assign("tomorrow_fin" , $tomorrow_fin);

$smarty->assign("listPrat", $listPrat);
$smarty->assign("listSpec", $listSpec);
$smarty->assign("listServ", $listServ);
$smarty->assign("filter"  , $filter);

$smarty->display("form_print_planning.tpl");

?>