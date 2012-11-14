<?php /* $Id: index.php 9159 2010-06-08 14:13:53Z flaviencrochard $ */

/**
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision: 9159 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_astreinte"             , TAB_READ);
$module->registerTab("vw_idx_plages_astreinte"        , TAB_EDIT);
?>