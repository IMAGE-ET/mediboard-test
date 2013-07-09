<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage PlanningOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$user = CMediusers::get();

// Si ni praticien ni admin, redirect
if (!$user->isPraticien() && CCanDo::checkAdmin()) {
  CAppUI::redirect();
}
 
// Gestion des bouton radio des dates
$now       = CMbDT::date();
$week_deb  = CMbDT::date("last sunday", $now);
$week_fin  = CMbDT::date("next sunday", $week_deb);
$week_deb  = CMbDT::date("+1 day"     , $week_deb);
$rectif     = CMbDT::transform("+0 DAY", $now, "%d")-1;
$month_deb  = CMbDT::date("-$rectif DAYS", $now);
$month_fin  = CMbDT::date("+1 month", $month_deb);
$month_fin  = CMbDT::date("-1 day", $month_fin);

// Chargement du filter permettant de faire la recherche
$filter = new COperation();
$filter->_date_min = $now;
$filter->_date_max = $now;

// Chargement de la liste de tous les praticiens
$praticien = new CMediusers();
$praticiens = array();

if ($user->isFromType(array("Administrator"))) {
  $praticiens = $praticien->loadPraticiens(PERM_EDIT);
}

if ($user->isPraticien()) {
  $praticiens[] = $user;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("filter", $filter);
$smarty->assign("now", $now);
$smarty->assign("week_deb", $week_deb);
$smarty->assign("week_fin", $week_fin);
$smarty->assign("month_deb", $month_deb);
$smarty->assign("month_fin", $month_fin);
$smarty->assign("praticiens", $praticiens);
$smarty->display("vw_edit_compta.tpl");
