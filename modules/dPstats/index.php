<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstats
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_hospitalisation", NULL, TAB_READ);
$module->registerTab("vw_bloc"           , NULL, TAB_READ);
$module->registerTab("vw_bloc2"          , NULL, TAB_READ);
$module->registerTab("vw_time_op"        , NULL, TAB_READ);
$module->registerTab("vw_personnel_salle", NULL, TAB_READ);
$module->registerTab("vw_users"          , NULL, TAB_ADMIN);

?>