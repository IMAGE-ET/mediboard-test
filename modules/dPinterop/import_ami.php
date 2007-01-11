<?php /* $Id: import_orl.php 783 2006-09-14 12:44:01Z rhum1 $ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision: 783 $
* @author Thomas Despoix
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
	$AppUI->redirect( "m=system&a=access_denied" );
}

// Création du template
$smarty = new CSmartyDP(1);

$smarty->display("import_ami.tpl");

?>