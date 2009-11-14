<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage system
* @version $Revision$
* @author Romain Ollivier
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("view_modules"        , TAB_EDIT);
$module->registerTab("view_messages"       , TAB_READ);
$module->registerTab("view_translate"      , TAB_READ);
$module->registerTab("object_merger"       , TAB_READ);
$module->registerTab("view_history"        , TAB_READ);
$module->registerTab("view_access_logs"    , TAB_READ);
$module->registerTab("view_ressources_logs", TAB_READ);
$module->registerTab("about"               , TAB_READ);

?>