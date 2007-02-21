<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Romain Ollivier
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_idx_planning"   , null, TAB_EDIT);
$module->registerTab("vw_edit_planning"  , null, TAB_EDIT);
$module->registerTab("vw_edit_sejour"    , null, TAB_READ);
$module->registerTab("vw_edit_urgence"   , null, TAB_READ);
$module->registerTab("vw_protocoles"     , null, TAB_EDIT);
$module->registerTab("vw_edit_protocole" , null, TAB_EDIT);
$module->registerTab("vw_edit_typeanesth", null, TAB_ADMIN);

?>