<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPrepas
* @version $Revision$
* @author Sébastien Fillonneau
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_edit_menu"       , TAB_EDIT);
$module->registerTab("vw_planning_repas"  , TAB_READ);
$module->registerTab("vw_edit_repas"      , TAB_EDIT);
$module->registerTab("vw_quantite"        , TAB_EDIT);
$module->registerTab("vw_create_archive"  , TAB_ADMIN);
?>