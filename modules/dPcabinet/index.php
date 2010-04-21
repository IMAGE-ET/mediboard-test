<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_planning"               , TAB_READ);
$module->registerTab("vw_journee"                , TAB_READ);
$module->registerTab("edit_planning"             , TAB_READ);
$module->registerTab("edit_consultation"         , TAB_EDIT);
$module->registerTab("vw_dossier"                , TAB_EDIT);
$module->registerTab("form_print_plages"         , TAB_READ);
$module->registerTab("vw_compta"                 , TAB_EDIT);
$module->registerTab("vw_edit_tarifs"            , TAB_EDIT);
$module->registerTab("vw_categories"             , TAB_EDIT);
$module->registerTab("vw_banques"                , TAB_ADMIN);
$module->registerTab("offline_programme_consult" , TAB_ADMIN);

if (CAppUI::pref("GestionFSE")) {
  $module->registerTab("vw_intermax" , TAB_EDIT);
}

?>