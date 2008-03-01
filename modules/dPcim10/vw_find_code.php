<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcim10
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsRead();

$lang   = mbGetValueFromGetOrSession("lang", CCodeCIM10::LANG_FR);
$code   = mbGetValueFromGetOrSession("code", "");
$keys   = mbGetValueFromGetOrSession("keys", "");
$level1 = mbGetValueFromGetOrSession("level1", "");

if(mbGetValueFromSession("code") || mbGetValueFromSession("keys")) {
  $level1 = "";
  mbSetValueToSession("level1");
}

$cim10 = new CCodeCIM10();

$sommaire = $cim10->getSommaire($lang);

$master = array();

if($code || $keys) {
  $master = $cim10->findCodes($code, $keys, $lang);
} elseif($level1) {
  $master = $cim10->getSubCodes($level1, $lang);
}

$numresults = count($master);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("cim10"     , $cim10);
$smarty->assign("lang"      , $lang);
$smarty->assign("code"      , $code);
$smarty->assign("keys"      , $keys);
$smarty->assign("level1"    , $level1);
$smarty->assign("sommaire"  , $sommaire);
$smarty->assign("master"    , $master);
$smarty->assign("numresults", $numresults);

$smarty->display("vw_find_code.tpl");

?>