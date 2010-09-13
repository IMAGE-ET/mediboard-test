<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPccam
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_find_code"  , TAB_READ);
$module->registerTab("vw_full_code"  , TAB_READ);
$module->registerTab("vw_idx_favoris", TAB_READ);
$module->registerTab("vw_idx_frais_divers_types", TAB_ADMIN);

?>