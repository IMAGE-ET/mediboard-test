<?php /* $Id$*/

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Romain Ollivier
*/
 
global $AppUI, $can, $m, $g;

$can->needsRead();

$sejour = new CSejour();

$listPrat = new CMediusers();
$listPrat = $listPrat->loadPraticiens(PERM_READ);

$listSpec = new CFunctions();
$listSpec = $listSpec->loadSpecialites(PERM_READ);

$listServ = new CService();
$where = array();
$where["group_id"] = "= '$g'";
$listServ = $listServ->loadListWithPerms(PERM_READ,$where);

$today      = mbDate();
$tomorrow   = mbDate("+1 day", $today);

$today_deb  = "$today 06:00:00";
$today_fin  = "$today 21:00:00";

$tomorrow_deb = "$tomorrow 06:00:00";
$tomorrow_fin = "$tomorrow 21:00:00";


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("today_deb"    , $today_deb);
$smarty->assign("today_fin"    , $today_fin);
$smarty->assign("tomorrow_deb" , $tomorrow_deb);
$smarty->assign("tomorrow_fin" , $tomorrow_fin);

$smarty->assign("listPrat", $listPrat);
$smarty->assign("listSpec", $listSpec);
$smarty->assign("listServ", $listServ);
$smarty->assign("sejour"  , $sejour);

$smarty->display("form_print_planning.tpl");

?>