<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Romain Ollivier
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_edit_prescription", null, TAB_READ);
$module->registerTab("vw_edit_category", null, TAB_READ);
$module->registerTab("vw_edit_element", null, TAB_READ);
$module->registerTab("vw_edit_executant", null, TAB_READ);
$module->registerTab("vw_edit_protocole", null, TAB_READ);
$module->registerTab("vw_edit_associations_moments", null, TAB_ADMIN);

?>