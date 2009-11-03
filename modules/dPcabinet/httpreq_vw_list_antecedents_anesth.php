<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Sébastien Fillonneau
*/

global $AppUI, $can, $m;
  
$can->needsEdit();

$sejour_id = CValue::getOrSession("sejour_id", 0);

// Chargement du sejour
$sejour = new CSejour();
$sejour->load($sejour_id);

// Chargement du dossier medical
$sejour->loadRefDossierMedical();
$dossier_medical =& $sejour->_ref_dossier_medical;

// Chargement des antecedents et traitements
$dossier_medical->loadRefsAntecedents(true);
$dossier_medical->countAntecedents();
foreach ($dossier_medical->_ref_antecedents as &$type) {
  foreach ($type as &$ant) {
    $ant->loadLogs();
  }
}

$dossier_medical->loadRefsTraitements();

// Chargement des prescriptions du sejour
$prescription = new CPrescription();
$sejour->loadRefsPrescriptions();
if($sejour->_ref_prescriptions && array_key_exists("sejour", $sejour->_ref_prescriptions)){
  $prescription = $sejour->_ref_prescriptions["sejour"];
}

// Chargement des lignes de tp de la prescription
$lines_tp = array();
if($prescription->_id){
	$line_tp = new CPrescriptionLineMedicament();
	$line_tp->prescription_id = $prescription->_id;
	$line_tp->traitement_personnel = 1;
	$lines_tp = $line_tp->loadMatchingList();
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("sejour", $sejour);
$smarty->assign("lines_tp", $lines_tp);
$smarty->display("inc_list_ant_anesth.tpl");

?>