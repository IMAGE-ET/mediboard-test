<?php /* $Id: */

/**
* @package Mediboard
* @subpackage dPbloc
* @version $Revision$
* @author Romain Ollivier
*/
 
global $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
	$AppUI->redirect( "m=system&a=access_denied" );
}

$now       = mbDate();

$week_deb  = mbDate("last sunday", $now);
$week_fin  = mbDate("next sunday", $week_deb);
$week_deb  = mbDate("+1 day"     , $week_deb);

$rectif     = mbTranformTime("+0 DAY", $now, "%d")-1;
$month_deb  = mbDate("-$rectif DAYS", $now);
$month_fin  = mbDate("+1 month", $month_deb);
$month_fin  = mbDate("-1 day", $month_fin);

$listPrat = new CMediusers();
$listPrat = $listPrat->loadPraticiens(PERM_READ);

$listSpec = new CFunctions();
$listSpec = $listSpec->loadSpecialites(PERM_READ);


$order = "nom";
$listSalles = new CSalle();
$listSalles = $listSalles->loadList(null, $order);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("now"       , $now);
$smarty->assign("week_deb"  , $week_deb);
$smarty->assign("week_fin"  , $week_fin);
$smarty->assign("month_deb" , $month_deb);
$smarty->assign("month_fin" , $month_fin);
$smarty->assign("listPrat"  , $listPrat);
$smarty->assign("listSpec"  , $listSpec);
$smarty->assign("listSalles", $listSalles);

$smarty->display("print_planning.tpl");

?>