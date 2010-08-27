<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage pharmacie
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_idx_prescriptions_sejour", TAB_READ);
$module->registerTab("vw_idx_dispensation"        , TAB_READ);
$module->registerTab("vw_idx_delivrance"          , TAB_READ);
$module->registerTab("vw_idx_outflow"             , TAB_READ);
$module->registerTab("vw_idx_balance"             , TAB_READ);

?>