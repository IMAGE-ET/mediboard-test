<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m, $tab;

$chir_id    = mbGetValueFromGet("chir_id"    , 0 );
$codes      = mbGetValueFromGet("codes"      , "");
$javascript = mbGetValueFromGet("javascript" , true);

$arrayCodes = explode("|", $codes);
$result = CTempsHospi::getTime($chir_id, $arrayCodes);
if($result) {
  $temps = sprintf("%.2f", $result)."j";
} else {
  $temps = "-";
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("temps", $temps);
$smarty->assign("javascript", $javascript);

$smarty->display("inc_get_time.tpl");

?>