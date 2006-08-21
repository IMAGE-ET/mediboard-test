<?php /* $Id: configure.php 23 2006-05-04 15:05:35Z MyttO $ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: 23 $
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

if(!$canEdit) {
	$AppUI->redirect("m=system&a=access_denied");
}

// Création du template
require_once($AppUI->getSystemClass("smartydp"));
$smarty = new CSmartyDP(1);

$smarty->display("files_integrity.tpl");

?>