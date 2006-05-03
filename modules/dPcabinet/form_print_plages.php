<?php /* $Id: form_print_plages.php,v 1.7 2006/04/21 16:56:07 mytto Exp $*/

/**
* @package Mediboard
* @subpackage dPbloc
* @version $Revision: 1.7 $
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
$smarty = new CSmartyDP;

$smarty->assign('deb', $deb);
$smarty->assign('fin', $fin);
$smarty->assign('listChir', $listChir);

$smarty->display('form_print_plages.tpl');

?>