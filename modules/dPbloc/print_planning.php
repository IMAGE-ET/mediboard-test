<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
global $AppUI, $can;
$can->needsRead();

$now = mbDate();

$filter = new COperation;
$filter->_date_min     = CValue::get("_date_min", $now);
$filter->_date_max     = CValue::get("_date_max", $now);
$filter->_prat_id      = CValue::getOrSession("_prat_id");
$filter->salle_id      = CValue::getOrSession("salle_id");
$filter->_plage        = CValue::getOrSession("_plage");
$filter->_intervention = CValue::getOrSession("_intervention");
$filter->_specialite   = CValue::getOrSession("_specialite");
$filter->_codes_ccam   = CValue::getOrSession("_codes_ccam");
$filter->_ccam_libelle = CValue::getOrSession("_ccam_libelle");

$filterSejour = new CSejour;
$filterSejour->type = CValue::getOrSession("type");

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

$bloc = new CBlocOperatoire();
$listBlocs = $bloc->loadListWithPerms(PERM_READ, null, "nom");
foreach($listBlocs as &$bloc) {
  $bloc->loadRefsSalles();
}
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
$smarty->assign("listBlocs"    , $listBlocs);

$smarty->display("print_planning.tpl");

?>