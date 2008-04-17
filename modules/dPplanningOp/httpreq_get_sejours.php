<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI, $can, $m, $tab;

$patient_id = mbGetValueFromGet("patient_id", 0);
$patient = new CPatient;
$patient->load($patient_id);
$patient->loadRefsSejours();

// Liste des Etablissements selon Permissions
$etablissements = new CMediusers();
$etablissements = $etablissements->loadEtablissements(PERM_READ);

// Creation d'un tableau permettant d'anticiper les collisions entre sejours
$sejour_collision = array();
if($patient->_ref_sejours){
	foreach($patient->_ref_sejours as $key => $_sejour){
	  if($_sejour->annule) {
	    unset($patient->_ref_sejours[$key]);
	  } else {
      $sejour_collision[$_sejour->_id]["entree_prevue"] = mbDate($_sejour->entree_prevue);
      $sejour_collision[$_sejour->_id]["sortie_prevue"] = mbDate($_sejour->sortie_prevue);
	  }
  }
}


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("sejour_collision", $sejour_collision);
$smarty->assign("sejours", $patient->_ref_sejours);
$smarty->assign("etablissements", $etablissements);

$smarty->display("inc_get_sejours.tpl");

?>