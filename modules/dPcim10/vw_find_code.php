<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPcim10
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can, $m;

$can->needsRead();

$lang   = CValue::getOrSession("lang", CCodeCIM10::LANG_FR);
$code   = CValue::getOrSession("code", "");
$keys   = CValue::getOrSession("keys", "");
$level1 = CValue::getOrSession("level1", "");
$level2 = CValue::getOrSession("level2", "");

if(CValue::session("code") || CValue::session("keys")) {
  $level1 = "";
  CValue::setSession("level1");
}
if(!$level1) {
  $level2 = "";
  CValue::setSession("level2");
}

$cim10 = new CCodeCIM10();

$listLevel1 = $cim10->getSommaire($lang);
$listLevel2 = array();

$master = array();

if($code || $keys) {
  $master = $cim10->findCodes($code, $keys, $lang);
} elseif($level2) {
  $listLevel2 = $cim10->getSubCodes($level1, $lang);
  $master = $cim10->getSubCodes($level2, $lang);
} elseif($level1) {
  $listLevel2 = $cim10->getSubCodes($level1, $lang);
  $master = $listLevel2;
}

$numresults = count($master);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("cim10"     , $cim10);
$smarty->assign("lang"      , $lang);
$smarty->assign("code"      , $code);
$smarty->assign("keys"      , $keys);
$smarty->assign("level1"    , $level1);
$smarty->assign("level2"    , $level2);
$smarty->assign("listLevel1", $listLevel1);
$smarty->assign("listLevel2", $listLevel2);
$smarty->assign("master"    , $master);
$smarty->assign("numresults", $numresults);

$smarty->display("vw_find_code.tpl");

?>