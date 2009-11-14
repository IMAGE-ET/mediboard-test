<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpmsi
* @version $Revision$
* @author Romain Ollivier
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_dossier"         , TAB_READ);
$module->registerTab("edit_actes"         , TAB_READ);
$module->registerTab("labo_groupage"      , TAB_READ);
$module->registerTab("vw_list_hospi"      , TAB_READ);
$module->registerTab("vw_list_interv"     , TAB_READ);
$module->registerTab("form_print_planning", TAB_READ);

?>