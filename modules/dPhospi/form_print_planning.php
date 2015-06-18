<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Hospi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$group = CGroups::loadCurrent();

$filter = new CSejour();
$today      = CMbDT::date();
$filter->_date_min     = CValue::getOrSession("_date_min", "$today 06:00:00");
$filter->_date_max     = CValue::getOrSession("_date_max", "$today 21:00:00");
$filter->_horodatage   = CValue::getOrSession("_horodatage", "entree_prevue");
$filter->_admission    = CValue::getOrSession("_admission");
$filter->_service      = CValue::getOrSession("_service");
$filter->praticien_id  = CValue::getOrSession("praticien_id");
$filter->convalescence = CValue::getOrSession("convalescence");
$filter->_specialite   = CValue::getOrSession("_specialite");
$filter->_filter_type  = CValue::getOrSession("_filter_type");
$filter->_ccam_libelle = CValue::getOrSession("_ccam_libelle", "1");
$filter->_coordonnees  = CValue::getOrSession("_coordonnees");
$filter->_notes        = CValue::getOrSession("_notes");
$filter->_by_date      = CValue::getOrSession("_by_date");

$listPrat = new CMediusers();
$listPrat = $listPrat->loadPraticiens(PERM_READ);

$listSpec = new CFunctions();
$listSpec = $listSpec->loadSpecialites(PERM_READ);

// Récupération de la liste des services
$where = array();
$where["externe"]    = "= '0'";
$where["cancelled"]  = "= '0'";
$service = new CService();
$services = $service->loadGroupList($where);

$yesterday  = CMbDT::date("-1 day", $today);
$tomorrow   = CMbDT::date("+1 day", $today);
$j2         = CMbDT::date("+2 day", $today);
$j3         = CMbDT::date("+3 day", $today);

$week_deb  = CMbDT::date("last sunday", $today);
$week_fin  = CMbDT::date("next sunday", $week_deb);
$week_deb  = CMbDT::date("+1 day"     , $week_deb);

$next_week_deb = CMbDT::date("+1 day"     , $week_fin);
$next_week_fin = CMbDT::date("next sunday", $next_week_deb);

$yesterday_deb  = "$yesterday 06:00:00";
$yesterday_fin  = "$yesterday 21:00:00";
$today_deb      = "$today 06:00:00";
$today_fin      = "$today 21:00:00";
$tomorrow_deb   = "$tomorrow 06:00:00";
$tomorrow_fin   = "$tomorrow 21:00:00";
$j2_deb         = "$j2 06:00:00";
$j2_fin         = "$j2 21:00:00";
$j3_deb         = "$j3 06:00:00";
$j3_fin         = "$j3 21:00:00";
$next_week_deb  = "$next_week_deb 06:00:00";
$next_week_fin  = "$next_week_fin 21:00:00";

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("yesterday_deb", $yesterday_deb);
$smarty->assign("yesterday_fin", $yesterday_fin);
$smarty->assign("today_deb"    , $today_deb);
$smarty->assign("today_fin"    , $today_fin);
$smarty->assign("tomorrow_deb" , $tomorrow_deb);
$smarty->assign("tomorrow_fin" , $tomorrow_fin);
$smarty->assign("j2_deb"       , $j2_deb);
$smarty->assign("j2_fin"       , $j2_fin);
$smarty->assign("j3_deb"       , $j3_deb);
$smarty->assign("j3_fin"       , $j3_fin);
$smarty->assign("next_week_deb", $next_week_deb);
$smarty->assign("next_week_fin", $next_week_fin);

$smarty->assign("listPrat", $listPrat);
$smarty->assign("listSpec", $listSpec);
$smarty->assign("listServ", $services);
$smarty->assign("filter"  , $filter);

$smarty->display("form_print_planning.tpl");