<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage developpement
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$module = CModule::getInstalled(basename(__DIR__));

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
$module->registerTab("cache_tester"           , TAB_READ);
//$module->registerTab("check_zombie_objects"   , TAB_READ);
$module->registerTab("benchmark"              , TAB_READ);
$module->registerTab("routage"                , TAB_READ);
$module->registerTab("vw_performance_profiling_analyzer", TAB_READ);
