<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab('vw_idx_order_manager', TAB_EDIT);
$module->registerTab('vw_idx_reception',     TAB_EDIT);
$module->registerTab('vw_idx_stock_group',   TAB_EDIT);
$module->registerTab('vw_idx_stock_service', TAB_EDIT);
//$module->registerTab('vw_idx_discrepancy',   TAB_EDIT);
$module->registerTab('vw_idx_reference',     TAB_EDIT);
$module->registerTab('vw_idx_product',       TAB_EDIT);
$module->registerTab('vw_idx_category',      TAB_EDIT);
$module->registerTab('vw_idx_societe',       TAB_EDIT);
$module->registerTab('vw_idx_stock_location',TAB_EDIT);
$module->registerTab('vw_idx_selection',     TAB_EDIT);
$module->registerTab('vw_idx_endowment',     TAB_EDIT);
$module->registerTab('vw_idx_report',        TAB_READ);
//$module->registerTab('vw_traceability',      TAB_READ);

?>