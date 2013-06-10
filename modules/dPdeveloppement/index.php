<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPdeveloppement
* @version $Revision$
* @author Sébastien Fillonneau
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("view_logs"              , TAB_READ);
$module->registerTab("view_metrique"          , TAB_READ);
$module->registerTab("mnt_module_actions"     , TAB_READ);
$module->registerTab("mnt_table_classes"      , TAB_READ);
$module->registerTab("mnt_backref_classes"    , TAB_READ);
$module->registerTab("mnt_traduction_classes" , TAB_EDIT);
$module->registerTab("vw_create_module"       , TAB_EDIT);
//$module->registerTab("launch_tests"         , TAB_READ);
$module->registerTab("css_test"               , TAB_READ);
$module->registerTab("form_tester"            , TAB_READ);
$module->registerTab("sniff_code"             , TAB_READ);
$module->registerTab("regression_checker"     , TAB_READ);
$module->registerTab("mutex_tester"           , TAB_READ);
$module->registerTab("check_zombie_objects"   , TAB_READ);
$module->registerTab("benchmark"              , TAB_READ);
