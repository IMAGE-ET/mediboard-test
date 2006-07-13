<?php /* $Id$*/

/**
* @package Mediboard
* @subpackage dPbloc
* @version $Revision$
* @author Romain Ollivier
*/
 
GLOBAL $AppUI, $canRead, $canEdit, $m;
require_once( $AppUI->getModuleClass('dPcabinet', 'plageconsult') );
require_once( $AppUI->getModuleClass('mediusers') );
require_once( $AppUI->getModuleClass('mediusers', 'functions') );
require_once( $AppUI->getModuleClass('mediusers', 'groups') );

if (!$canRead) {
	$AppUI->redirect( "m=system&a=access_denied" );
}

$deb = mbDate();
$fin = mbDate("+ 0 day");

// Liste des praticiens
$mediusers = new CMediusers();
$listChir = $mediusers->loadPraticiens(PERM_EDIT);

// Création du template
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP(1);

$smarty->assign('deb', $deb);
$smarty->assign('fin', $fin);
$smarty->assign('listChir', $listChir);

$smarty->display('form_print_plages.tpl');

?>