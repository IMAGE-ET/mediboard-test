<?php

/**
 * dPbloc
 *
 * @category Bloc
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$now = CMbDT::date();

$filter = new COperation();
$filter->_datetime_min = CValue::getOrSession("_datetime_min", "$now 00:00:00");
$filter->_datetime_max = CValue::getOrSession("_datetime_max", "$now 23:59:59");
$filter->_prat_id      = CValue::getOrSession("_prat_id");
$filter->salle_id      = CValue::getOrSession("salle_id");
$filter->_plage        = CValue::getOrSession("_plage", CAppUI::conf("dPbloc CPlageOp plage_vide"));
$filter->_ranking      = CValue::getOrSession("_ranking");
$filter->_specialite   = CValue::getOrSession("_specialite");
$filter->_codes_ccam   = CValue::getOrSession("_codes_ccam");
$filter->_ccam_libelle = CValue::getOrSession("_ccam_libelle", CAppUI::conf("dPbloc CPlageOp libelle_ccam"));

$filterSejour = new CSejour();
$filterSejour->type = CValue::getOrSession("type");

$tomorrow  = CMbDT::date("+1 day", $now);
$j2        = CMbDT::date("+2 day", $now);
$j3        = CMbDT::date("+3 day", $now);

$week_deb  = CMbDT::date("last sunday", $now);
$week_fin  = CMbDT::date("next sunday", $week_deb);
$week_deb  = CMbDT::date("+1 day"     , $week_deb);

$next_week_deb = CMbDT::date("+1 day"     , $week_fin);
$next_week_fin = CMbDT::date("next sunday", $next_week_deb);

$rectif     = CMbDT::transform("+0 DAY", $now, "%d")-1;
$month_deb  = CMbDT::date("-$rectif DAYS", $now);
$month_fin  = CMbDT::date("+1 month", $month_deb);
$month_fin  = CMbDT::date("-1 day", $month_fin);

$next_month_deb = CMbDT::date("+1 day", $month_fin);
$next_month_fin = CMbDT::date("+1 month", $month_fin);
$next_month_fin = CMbDT::date("-1 day", $next_month_fin);

$listPrat = new CMediusers();
$listPrat = $listPrat->loadPraticiens(PERM_READ);

$listSpec = new CFunctions();
$listSpec = $listSpec->loadSpecialites(PERM_READ, 1);

$bloc = new CBlocOperatoire();
$group = CGroups::loadCurrent();
$where = array();
$where["group_id"] = "= '$group->_id'";
/** @var CBlocOperatoire[] $listBlocs */
$listBlocs = $bloc->loadListWithPerms(PERM_READ, $where, "nom");
foreach ($listBlocs as &$bloc) {
  $bloc->loadRefsSalles();
}

$praticien = CMediusers::get();
// Création du template
$smarty = new CSmartyDP("modules/dPbloc");

$smarty->assign("praticien"    , $praticien);
$smarty->assign("chir"         , $praticien->user_id);
$smarty->assign("filter"       , $filter);
$smarty->assign("filterSejour" , $filterSejour);
$smarty->assign("now"          , $now);
$smarty->assign("tomorrow"     , $tomorrow);
$smarty->assign("j2"           , $j2);
$smarty->assign("j3"           , $j3);
$smarty->assign("next_week_deb", $next_week_deb);
$smarty->assign("next_week_fin", $next_week_fin);
$smarty->assign("week_deb"     , $week_deb);
$smarty->assign("week_fin"     , $week_fin);
$smarty->assign("month_deb"    , $month_deb);
$smarty->assign("month_fin"    , $month_fin);
$smarty->assign("next_month_deb", $next_month_deb);
$smarty->assign("next_month_fin", $next_month_fin);
$smarty->assign("listPrat"     , $listPrat);
$smarty->assign("listSpec"     , $listSpec);
$smarty->assign("listBlocs"    , $listBlocs);

$smarty->display("print_planning.tpl");
