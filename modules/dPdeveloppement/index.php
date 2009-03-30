<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPdeveloppement
* @version $Revision: $
* @author Sébastien Fillonneau
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("view_metrique"       		, null, TAB_READ);
$module->registerTab("view_logs"           		, null, TAB_READ);
$module->registerTab("mnt_module_actions"  		, null, TAB_READ);
$module->registerTab("mnt_table_classes"   		, null, TAB_READ);
$module->registerTab("mnt_backref_classes" 		, null, TAB_READ);
$module->registerTab("mnt_traduction_classes" , null, TAB_READ);
//$module->registerTab("echantillonnage"     		, null, TAB_READ);
$module->registerTab("launch_tests"        		, null, TAB_READ);
$module->registerTab("form_tester"            , null, TAB_READ);
$module->registerTab("mutex_tester"           , null, TAB_READ);
$module->registerTab("check_dossiers_medicaux", null, TAB_READ);
$module->registerTab("check_zombie_objects"   , null, TAB_READ);

?>