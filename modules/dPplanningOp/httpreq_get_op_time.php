<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m, $tab;

$chir_id = CValue::get("chir_id" , 0 );
$codes   = CValue::get("codes"   , "");
$javascript = CValue::get("javascript" , true);

$arrayCodes = explode("|", $codes);
$result = CTempsOp::getTime($chir_id, $arrayCodes);
if($result) {
  $temps = strftime("%Hh%M", $result);
} else {
  $temps = "-";
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("temps", $temps);
$smarty->assign("javascript", $javascript);

$smarty->display("inc_get_time.tpl");

?>