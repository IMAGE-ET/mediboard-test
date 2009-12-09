<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstats
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_hospitalisation"     , TAB_READ);
$module->registerTab("vw_bloc"                , TAB_READ);
$module->registerTab("vw_cancelled_operations", TAB_READ);
$module->registerTab("vw_reveil"              , TAB_READ);
$module->registerTab("vw_bloc2"               , TAB_READ);
$module->registerTab("vw_time_op"             , TAB_READ);
$module->registerTab("vw_personnel_salle"     , TAB_READ);
$module->registerTab("vw_users"               , TAB_ADMIN);

?>