<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPdeveloppement
* @version $Revision: $
* @author Sébastien Fillonneau
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("view_metrique"       , "Métrique"              , TAB_READ);
$module->registerTab("view_logs"           , "Logs système"          , TAB_READ);
$module->registerTab("mnt_table_classes"   , "Maintenance Table"     , TAB_READ);
$module->registerTab("mnt_backref_classes" , "Maintenance Classes"   , TAB_READ);
$module->registerTab("vw_refMandatory"     , "Prop RefMandatory"     , TAB_READ);
$module->registerTab("echantillonnage"     , "Echantillonnage"       , TAB_READ);
?>