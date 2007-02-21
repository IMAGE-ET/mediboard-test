<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_idx_patients" , null, TAB_READ);
$module->registerTab("vw_full_patients", null, TAB_READ);
$module->registerTab("vw_edit_patients", null, TAB_EDIT);
$module->registerTab("vw_medecins"     , null, TAB_EDIT);

?>