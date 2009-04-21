<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Alexis Granger
*/

global $can;

$can->needsEdit();

$prescription_id = mbGetValueFromGetOrSession("prescription_id");

$prescription = new CPrescription();
$prescription->load($prescription_id);

$smarty = new CSmartyDP();
$smarty->assign("prescription", $prescription);
$smarty->assign("nodebug", true);
$smarty->display("inc_vw_protocole_anesth.tpl");

?>