<?php /* $Id: import_dermato.php,v 1.2 2006/04/21 16:56:38 mytto Exp $ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision: 1.2 $
* @author Thomas Despoix
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
	$AppUI->redirect( "m=system&a=access_denied" );
}

// Création du template
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP;

$smarty->display('import_dermato.tpl');

?>