<?php /* $Id$*/

/**
* @package Mediboard
* @subpackage dPbloc
* @version $Revision$
* @author Romain Ollivier
*/
 
global $AppUI, $can, $m;

$can->needsRead();

$now       = mbDate();
$tomorrow  = mbDate("+1 day", $now);

$week_deb  = mbDate("last sunday", $now);
$week_fin  = mbDate("next sunday", $week_deb);
$week_deb  = mbDate("+1 day"     , $week_deb);

$rectif     = mbTranformTime("+0 DAY", $now, "%d")-1;
$month_deb  = mbDate("-$rectif DAYS", $now);
$month_fin  = mbDate("+1 month", $month_deb);
$month_fin  = mbDate("-1 day", $month_fin);

// Liste des praticiens
$mediusers = new CMediusers();
$listChir = $mediusers->loadPraticiens(PERM_EDIT);

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("now"       , $now);
$smarty->assign("tomorrow"  , $tomorrow);
$smarty->assign("week_deb"  , $week_deb);
$smarty->assign("week_fin"  , $week_fin);
$smarty->assign("month_deb" , $month_deb);
$smarty->assign("month_fin" , $month_fin);
$smarty->assign("listChir"  , $listChir);

$smarty->display("form_print_plages.tpl");

?>