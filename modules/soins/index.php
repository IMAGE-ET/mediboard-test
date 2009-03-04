<?php /* $Id:  $ */

/**
 *	@package Mediboard
 *	@subpackage soins
 *	@version $Revision:  $
 *  @author Alexis Granger
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_idx_sejour", null, TAB_READ);
$module->registerTab("vw_bilan_prescription", null, TAB_READ);
$module->registerTab("vw_pancarte_service", null, TAB_READ);

?>