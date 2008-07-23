<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage pharmacie
 *	@version $Revision: $
 *  @author Fabien Ménager
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_idx_prescriptions_sejour", null, TAB_READ);
$module->registerTab("vw_idx_dispensation", null, TAB_READ);
$module->registerTab("vw_idx_dispensation_nominative", null, TAB_READ);
$module->registerTab("vw_idx_delivrance", null, TAB_READ);
$module->registerTab("vw_idx_destockage_service", null, TAB_READ);

?>