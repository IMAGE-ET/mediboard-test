<?php /* $Id: index.php 1201 2006-10-26 18:33:50Z rhum1 $ */

/**
* @package Mediboard
* @subpackage sherpa
* @version $Revision: 1201 $
* @author Sherpa
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

//$module->registerTab("view_etablissements", null, TAB_READ);
$module->registerTab("view_malades", null, TAB_READ);
$module->registerTab("view_dossiers", null, TAB_READ);
$module->registerTab("view_sejours", null, TAB_READ);
$module->registerTab("view_droits", null, TAB_READ);
$module->registerTab("view_entccam", null, TAB_READ);
$module->registerTab("export_actes", null, TAB_EDIT);
$module->registerTab("view_idsherpa", null, TAB_EDIT);

?>