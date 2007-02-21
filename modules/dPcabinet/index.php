<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_planning"      , null , TAB_READ);
$module->registerTab("edit_planning"    , null , TAB_READ);
$module->registerTab("edit_consultation", null , TAB_EDIT);
$module->registerTab("vw_dossier"       , null , TAB_EDIT);
$module->registerTab("form_print_plages", null , TAB_READ);
$module->registerTab("vw_compta"        , null , TAB_EDIT);

?>