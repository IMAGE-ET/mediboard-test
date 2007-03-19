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

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("today"   , date("Y-m-d")." 06:00:00");
$smarty->assign("tomorrow", date("Y-m-d")." 21:00:00");

$smarty->assign("listPrat", $listPrat);
$smarty->assign("listSpec", $listSpec);
$smarty->assign("listServ", $listServ);
$smarty->assign("sejour"  , $sejour);

$smarty->display("form_print_planning.tpl");

?>