<?php /* $Id: */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision:$
* @author SARL Openxtrem
*/

$chir_id = CValue::get("chir_id");

$chir = new CMediusers;
$chir->load($chir_id);
$chir->loadRefFunction();

$_functions = $chir->loadBackRefs("secondary_functions");

$smarty = new CSmartyDP;

$smarty->assign("_functions", $_functions);
$smarty->assign("chir"      , $chir);

$smarty->display("inc_refresh_secondary_functions.tpl");
?>