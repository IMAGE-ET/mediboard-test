<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision:$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_idx_dest_hprim"            , TAB_ADMIN);
$module->registerTab("vw_idx_echange_hprim"         , TAB_ADMIN);
$module->registerTab("vw_optimization_echange_hprim", TAB_ADMIN);

?>