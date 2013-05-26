<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Stock
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab('vw_idx_order_manager', TAB_EDIT);
//$module->registerTab('vw_idx_reception',     TAB_EDIT);
$module->registerTab('vw_idx_stock_group',   TAB_EDIT);
$module->registerTab('vw_idx_stock_service', TAB_EDIT);
//$module->registerTab('vw_idx_discrepancy',   TAB_EDIT);
$module->registerTab('vw_idx_reference',     TAB_EDIT);
$module->registerTab('vw_idx_product',       TAB_EDIT);
//$module->registerTab('vw_idx_report',        TAB_READ);

$module->registerTab('vw_idx_setup',         TAB_EDIT);
//$module->registerTab('vw_idx_category',      TAB_EDIT);
//$module->registerTab('vw_idx_societe',       TAB_EDIT);
//$module->registerTab('vw_idx_stock_location',TAB_EDIT);
//$module->registerTab('vw_idx_selection',     TAB_EDIT);
//$module->registerTab('vw_idx_endowment',     TAB_EDIT);

//$module->registerTab('vw_traceability',      TAB_READ);

