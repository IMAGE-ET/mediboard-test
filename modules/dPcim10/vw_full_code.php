<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcim10
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
	$AppUI->redirect( "m=system&a=access_denied" );
}

$lang = mbGetValueFromGetOrSession("lang", CCodeCIM10::LANG_FR);

$code = mbGetValueFromGetOrSession("code", "(A00-B99)");
$cim10 = new CCodeCIM10($code);
$cim10->load($lang);
$cim10->loadRefs();

// Création du template
$smarty = new CSmartyDP(1);

$smarty->assign("lang" , $lang);
$smarty->assign("cim10", $cim10);

$smarty->display("vw_full_code.tpl");

?>