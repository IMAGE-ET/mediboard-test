<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ecap
 * @version $Revision: $
 * @author Thomas Despoix
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_identifiants", null, TAB_EDIT);

?>