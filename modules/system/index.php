<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcim10
* @version $Revision$
* @author Romain Ollivier
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("view_modules"        , null, TAB_EDIT);
$module->registerTab("view_messages"       , null, TAB_READ);
$module->registerTab("view_translate"      , null, TAB_READ);
$module->registerTab("object_merger"       , null, TAB_READ);
$module->registerTab("view_history"        , null, TAB_READ);
$module->registerTab("view_access_logs"    , null, TAB_READ);
$module->registerTab("view_ressources_logs", null, TAB_READ);
$module->registerTab("view_install"        , null, TAB_READ);

?>