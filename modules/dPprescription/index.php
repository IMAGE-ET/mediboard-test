<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Romain Ollivier
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

//$module->registerTab("vw_edit_prescription", null, TAB_READ);
$module->registerTab("vw_edit_protocole"           , null, TAB_EDIT);
$module->registerTab("vw_edit_liaison_admission"   , null, TAB_EDIT);
$module->registerTab("vw_edit_category"            , null, TAB_ADMIN);
$module->registerTab("vw_edit_element"             , null, TAB_ADMIN);
$module->registerTab("vw_edit_executant"           , null, TAB_ADMIN);
$module->registerTab("vw_edit_associations_moments", null, TAB_ADMIN);
$module->registerTab("vw_edit_moments_unitaires"   , null, TAB_ADMIN);
?>