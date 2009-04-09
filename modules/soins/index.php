<?php /* $Id:  $ */

/**
 * @package Mediboard
 * @subpackage soins
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_idx_sejour", null, TAB_READ);
$module->registerTab("vw_bilan_prescription", null, TAB_READ);
$module->registerTab("vw_pancarte_service", null, TAB_READ);

?>