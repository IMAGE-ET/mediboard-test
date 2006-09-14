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

$deb = mbDate();
$fin = mbDate("+ 0 day");

$listPrat = new CMediusers();
$listPrat = $listPrat->loadPraticiens(PERM_READ);

$listSpec = new CFunctions();
$listSpec = $listSpec->loadSpecialites(PERM_READ);


$order = "nom";
$listSalles = new CSalle();
$listSalles = $listSalles->loadList(null, $order);

// Création du template
$smarty = new CSmartyDP(1);

$smarty->assign("deb"       , $deb);
$smarty->assign("fin"       , $fin);
$smarty->assign("listPrat"  , $listPrat);
$smarty->assign("listSpec"  , $listSpec);
$smarty->assign("listSalles", $listSalles);

$smarty->display("print_planning.tpl");

?>