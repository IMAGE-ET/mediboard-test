<?php /* $Id: */

/**
* @package Mediboard
* @subpackage dPbloc
* @version $Revision: 1.8 $
* @author Romain Ollivier
*/
 
global $AppUI, $canRead, $canEdit, $m;

require_once($AppUI->getModuleClass($m, "salle"));
require_once($AppUI->getModuleClass("mediusers"));

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
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP;

$smarty->assign('deb', $deb);
$smarty->assign('fin', $fin);
$smarty->assign('listPrat', $listPrat);
$smarty->assign('listSpec', $listSpec);
$smarty->assign('listSalles', $listSalles);

$smarty->display('print_planning.tpl');

?>