<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcim10
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

if(!$canRead) {
	$AppUI->redirect( "m=system&a=access_denied" );
}

$lang = mbGetValueFromGetOrSession("lang", CCodeCIM10::LANG_FR);
$keys = mbGetValueFromGetOrSession("keys", "");

$cim10 = new CCodeCIM10();
$master = $cim10->findCodes($keys, $lang);

$numresults = count($master);

// Création du template
$smarty = new CSmartyDP(1);

$smarty->assign("cim10"      , $cim10);
$smarty->assign("lang"      , $lang);
$smarty->assign("keys"      , $keys);
$smarty->assign("master"    , $master);
$smarty->assign("numresults", $numresults);

$smarty->display("vw_find_code.tpl");

?>