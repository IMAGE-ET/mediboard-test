<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ecap
 * @version $Revision: $
 * @author Thomas Despoix
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_identifiants", null, TAB_EDIT);
$module->registerTab("vw_soap_services", null, TAB_EDIT);
$module->registerTab("export_documents", null, TAB_EDIT);
$module->registerTab("manage_categories", null, TAB_EDIT);

?>