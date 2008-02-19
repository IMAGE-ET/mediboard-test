<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpmsi
* @version $Revision$
* @author Romain Ollivier
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_dossier"         , null, TAB_READ);
$module->registerTab("edit_actes"         , null, TAB_READ);
$module->registerTab("labo_groupage"      , null, TAB_READ);
$module->registerTab("vw_list_hospi"      , null, TAB_READ);
$module->registerTab("vw_list_interv"     , null, TAB_READ);
$module->registerTab("form_print_planning", null, TAB_READ);

?>