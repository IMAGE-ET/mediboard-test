<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcim10
* @version $Revision$
* @author Romain Ollivier
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("view_dpadmin"    , "Configuration générale", TAB_READ);
$module->registerTab("view_modules"    , "Modules"               , TAB_READ);
$module->registerTab("view_history"    , "Historique"            , TAB_READ);
$module->registerTab("view_messages"   , "Messagerie"            , TAB_READ);
$module->registerTab("view_logs"       , "Logs système"          , TAB_READ);
$module->registerTab("view_access_logs", "Logs d'accès"          , TAB_READ);
$module->registerTab("view_metrique"   , "Métrique"              , TAB_READ);

?>
