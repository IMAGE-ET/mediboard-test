<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Romain Ollivier
*/

global $can;
$can->needsRead();

$patient_id = mbGetValueFromGet("patient_id");
$patient = new CPatient;
$patient->load($patient_id);
$patient->loadRefsSejours();

// Liste des Etablissements selon Permissions
$etablissements = new CMediusers();
$etablissements = $etablissements->loadEtablissements(PERM_READ);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("sejours_collision", $patient->getSejoursCollisions());
$smarty->assign("sejours", $patient->_ref_sejours);
$smarty->assign("etablissements", $etablissements);

$smarty->display("inc_get_sejours.tpl");

?>