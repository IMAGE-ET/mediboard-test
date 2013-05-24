<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hprim21
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_patients"             , TAB_READ);
$module->registerTab("pat_hprim_selector"      , TAB_READ);
$module->registerTab("vw_hprim_files"          , TAB_READ);
$module->registerTab("vw_display_hprim_message", TAB_READ);

