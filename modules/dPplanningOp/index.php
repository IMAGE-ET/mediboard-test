<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $dPconfig;
$user = new CMediusers();
$user->load($AppUI->user_id);

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_idx_planning"   , TAB_EDIT);
$module->registerTab("vw_edit_planning"  , TAB_EDIT);
$module->registerTab("vw_edit_sejour"    , TAB_READ);
$module->registerTab("vw_edit_urgence"   , TAB_READ);
$module->registerTab("vw_protocoles"     , TAB_EDIT);
$module->registerTab("vw_edit_protocole" , TAB_EDIT);
$module->registerTab("vw_edit_typeanesth", TAB_ADMIN);

// Droit d'acces a l'onglet seulement si on est praticien ou admin
if(($user->isPraticien() || $user->isFromType(array("Administrator"))) && $dPconfig["dPsalleOp"]["CActeCCAM"]["tarif"]) {
  $module->registerTab("vw_edit_compta", TAB_EDIT);
}

?>