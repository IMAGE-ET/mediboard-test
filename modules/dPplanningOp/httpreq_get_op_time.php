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
$result = CTempsOp::getTime($chir_id, $arrayCodes);
if($result) {
  $temps = strftime("%Hh%M", $result);
} else {
  $temps = "-";
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("temps", $temps);

$smarty->display("inc_get_time.tpl");

?>