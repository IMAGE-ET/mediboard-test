<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPsalleOp
* @version $Revision$
* @author Romain Ollivier
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_operations", null,TAB_READ);
$module->registerTab("vw_reveil"    , null, TAB_READ);

//$module->registerTab("vw_brancardage", "Brancardage"       , TAB_READ);

$module->registerTab("vw_urgences"    , null , TAB_READ);
$module->registerTab("vw_suivi_salles", null , TAB_READ);

//$module->registerTab("vw_anesthesie" , "Anesthésie"        , TAB_READ);

if(CAppUI::conf('dPsalleOp CActeCCAM signature')){
  $module->registerTab("vw_signature_actes", null, TAB_READ);
}

$module->registerTab("vw_daily_check_traceability", null, TAB_READ);
$module->registerTab("vw_daily_check_item_type"   , null, TAB_ADMIN);

?>