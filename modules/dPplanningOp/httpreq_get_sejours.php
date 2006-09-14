<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m, $tab;

$patient_id = mbGetValueFromGet("patient_id", 0);
$patient = new CPatient;
$patient->load($patient_id);
$patient->loadRefsSejours();

// Liste des Etablissements selon Permissions
$etablissements = new CMediusers();
$etablissements = $etablissements->loadEtablissements(PERM_READ);

// Création du template
$smarty = new CSmartyDP(1);

$smarty->assign("sejours", $patient->_ref_sejours);
$smarty->assign("etablissements", $etablissements);

$smarty->display("inc_get_sejours.tpl");

?>