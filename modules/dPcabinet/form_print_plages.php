<?php /* $Id$*/

/**
* @package Mediboard
* @subpackage dPbloc
* @version $Revision$
* @author Romain Ollivier
*/

CCanDo::checkRead();

$now       = CMbDT::date();

$filter = new CConsultation;
$filter->_date_min = CValue::get("_date_min"    , "$now");
$filter->_date_max = CValue::get("_date_max"    , "$now");

$tomorrow  = CMbDT::date("+1 day", $now);

$week_deb  = CMbDT::date("last sunday", $now);
$week_fin  = CMbDT::date("next sunday", $week_deb);
$week_deb  = CMbDT::date("+1 day"     , $week_deb);

$rectif     = CMbDT::transform("+0 DAY", $now, "%d")-1;
$month_deb  = CMbDT::date("-$rectif DAYS", $now);
$month_fin  = CMbDT::date("+1 month", $month_deb);
$month_fin  = CMbDT::date("-1 day", $month_fin);

// Liste des praticiens
$mediusers = new CMediusers();
if(CAppUI::pref("pratOnlyForConsult", 1)) {
  $listChir = $mediusers->loadPraticiens(PERM_READ);
} else {
  $listChir = $mediusers->loadProfessionnelDeSante(PERM_READ);
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("filter"  , $filter);
$smarty->assign("now"       , $now);
$smarty->assign("tomorrow"  , $tomorrow);
$smarty->assign("week_deb"  , $week_deb);
$smarty->assign("week_fin"  , $week_fin);
$smarty->assign("month_deb" , $month_deb);
$smarty->assign("month_fin" , $month_fin);
$smarty->assign("listChir"  , $listChir);

$smarty->display("form_print_plages.tpl");

?>