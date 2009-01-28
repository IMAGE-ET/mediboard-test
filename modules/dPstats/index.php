<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision$
* @author Romain Ollivier
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_hospitalisation", NULL, TAB_READ);
$module->registerTab("vw_bloc"           , NULL, TAB_READ);
$module->registerTab("vw_bloc2"          , NULL, TAB_READ);
$module->registerTab("vw_time_op"        , NULL, TAB_READ);
$module->registerTab("vw_personnel_salle", NULL, TAB_READ);
$module->registerTab("vw_users"          , NULL, TAB_ADMIN);

?>