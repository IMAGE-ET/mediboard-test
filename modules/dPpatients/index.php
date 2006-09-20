<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_idx_patients" , "Chercher un dossier"    , TAB_READ);
$module->registerTab("vw_full_patients", "Consulter un dossier"   , TAB_READ);
$module->registerTab("vw_edit_patients", "Edition d'un dossier"   , TAB_EDIT);
$module->registerTab("vw_medecins"     , "Médecins correspondants", TAB_EDIT);

?>