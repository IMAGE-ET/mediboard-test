<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m, $tab;

require_once($AppUI->getModuleClass("dPstats", "tempsOp"));

$chir_id = mbGetValueFromGet("chir_id" , 0 );
$codes   = mbGetValueFromGet("codes"   , "");

$arrayCodes = explode("|", $codes);
$temps = CTempsOp::getTime($chir_id, $arrayCodes);

// Création du template
require_once($AppUI->getSystemClass("smartydp"));
$smarty = new CSmartyDP(1);

$smarty->assign("temps", $temps);

$smarty->display("inc_get_op_time.tpl");

?>