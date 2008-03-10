<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPstock
 *	@version $Revision: $
 *  @author Fabien Ménager
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab('vw_idx_stock',         null, TAB_READ);
$module->registerTab('vw_idx_order_manager', null, TAB_READ);
$module->registerTab('vw_idx_product',       null, TAB_READ);
$module->registerTab('vw_idx_category',      null, TAB_READ);
$module->registerTab('vw_idx_societe',       null, TAB_READ);
$module->registerTab('vw_idx_reference',     null, TAB_READ);

?>