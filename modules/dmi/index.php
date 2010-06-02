<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_commandes" , TAB_READ);
$module->registerTab("vw_categories", TAB_EDIT);
$module->registerTab("vw_elements"  , TAB_EDIT);
$module->registerTab("vw_peremption", TAB_READ);

?>