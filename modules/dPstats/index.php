<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision$
* @author Romain Ollivier
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_hospitalisation", "Hospitalisation"   , TAB_READ);
$module->registerTab("vw_bloc"           , "Bloc opératoire"   , TAB_READ);
$module->registerTab("vw_bloc2"          , "Journée opératoire", TAB_READ);
$module->registerTab("vw_time_op"        , "Stats de durées"   , TAB_READ);
$module->registerTab("vw_users"          , "Utilisateurs"      , TAB_ADMIN);

?>