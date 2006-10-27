<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m, $tab;

$chir_id = mbGetValueFromGet("chir_id" , 0 );
$codes   = mbGetValueFromGet("codes"   , "");

$arrayCodes = explode("|", $codes);
$result = CTempsHospi::getTime($chir_id, $arrayCodes);
if($result) {
  $temps = sprintf("%.2f", $result)." jours";
} else {
  $temps = "-";
}

// Création du template
$smarty = new CSmartyDP(1);

$smarty->assign("temps", $temps);

$smarty->display("inc_get_time.tpl");

?>