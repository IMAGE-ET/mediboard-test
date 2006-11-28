<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcim10
* @version $Revision$
* @author Romain Ollivier
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("view_messages"       , "Messagerie"            , TAB_READ);
$module->registerTab("view_modules"        , "Modules"               , TAB_READ);
$module->registerTab("view_translate"      , "Traduction"            , TAB_READ);
$module->registerTab("view_install"        , "Installation"          , TAB_READ);
$module->registerTab("echantillonnage"     , "Echantillonnage"       , TAB_READ);
$module->registerTab("view_history"        , "Historique"            , TAB_READ);
$module->registerTab("view_access_logs"    , "Logs d'accès"          , TAB_READ);
$module->registerTab("view_ressources_logs", "Logs ressources"       , TAB_READ);
$module->registerTab("view_logs"           , "Logs système"          , TAB_READ);
$module->registerTab("mnt_table_classes"   , "Maintenance Table"     , TAB_READ);
$module->registerTab("view_metrique"       , "Métrique"              , TAB_READ);
?>