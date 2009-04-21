<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can, $m;

$can->needsRead();

$sejour_id = mbGetValueFromGetOrSession("sejour_id");

// Chargement de l'utilisateur courant
$user = new CMediusers();
$user->load($AppUI->user_id);
$is_praticien = $user->isPraticien();


// Chargement de la prescription
$prescription = new CPrescription();
$prescription->object_id = $sejour_id;
$prescription->object_class = "CSejour";
$prescription->type = "sejour";
$prescription->loadMatchingObject();


// Chargement des medicaments
if ($prescription->_id) {
  $prescription->loadRefsLinesMed();
  
  $lines = array();
	$lines["prescription"] = $prescription->_ref_prescription_lines;
  
  // Chargement des droits et des prises
  foreach($lines as $type => $type_line){
    foreach($type_line as &$line) {
      $line->loadRefsPrises();
      $line->loadRefParentLine();
      $line->getAdvancedPerms($is_praticien, "sejour");    
    }
  }

	// Calcul des alertes
	$sejour->loadRefPatient();
	$patient =& $sejour->_ref_patient;
	$patient->loadRefDossierMedical();
			  
	// Chargement du dossier medical
	$dossier_medical =& $patient->_ref_dossier_medical;
	$dossier_medical->updateFormFields();
	$dossier_medical->loadRefsAntecedents();
	$dossier_medical->loadRefsTraitements();
	$dossier_medical->countAntecedents();
	 
	// Calcul des alertes de la prescription
	$allergies    = new CBcbControleAllergie();
	$allergies->setPatient($patient);
	$profil       = new CBcbControleProfil();
	$profil->setPatient($patient);
	$interactions = new CBcbControleInteraction();
	$IPC          = new CBcbControleIPC();
	$surdosage    = new CBcbControleSurdosage();
	$surdosage->setPrescription($prescription);
	
	// Parcours des perfusions
	$prescription->loadRefsPerfusions();
	foreach($prescription->_ref_perfusions as $_perfusion){
	  $_perfusion->loadRefsLines();
	  foreach($_perfusion->_ref_lines as $_perf_line){
	    if($prescription->object_id){
	      $allergies->addProduit($_perf_line->code_cip);
	      $profil->addProduit($_perf_line->code_cip);
	    }			    
	   $interactions->addProduit($_perf_line->code_cip);
	   $IPC->addProduit($_perf_line->code_cip);
	  }
	}
	
	// Parcours des lignes (medicament + tp)
	foreach($lines as $type => $type_line){
	  foreach($type_line as &$line) {
		  if($prescription->object_id){
		    $allergies->addProduit($line->code_cip);
		    $profil->addProduit($line->code_cip);
		  }			    
		  $interactions->addProduit($line->code_cip);
		  $IPC->addProduit($line->code_cip);
	  }
	}
	
	// Calcul
	$alertesAllergies    = $allergies->getAllergies();
	$alertesProfil       = $profil->getProfil();	  
	$alertesInteractions = $interactions->getInteractions();
	$alertesIPC          = $IPC->getIPC();
	$alertesPosologies   = $surdosage->getSurdosage();
		  
	$prescription->_scores["hors_livret"] = 0;
	foreach($lines as $type_line){
	  foreach($type_line as &$line) {
	    $prescription->checkAllergies($alertesAllergies, $line->code_cip);
	    $prescription->checkProfil($alertesProfil, $line->code_cip);      
	    $prescription->checkIPC($alertesIPC, $line->code_cip);
	    $prescription->checkInteractions($alertesInteractions, $line->code_cip);
	    $prescription->checkPoso($alertesPosologies, $line->code_cip);
	    if(!$line->_ref_produit->inLivret){
	      $prescription->_scores["hors_livret"]++;
	    }
	  }
	}
	foreach($prescription->_ref_perfusions as $_perfusion){
		foreach($_perfusion->_ref_lines as $_perf_line){
		  $prescription->checkAllergies($alertesAllergies, $_perf_line->code_cip);
		  $prescription->checkProfil($alertesProfil, $_perf_line->code_cip); 
	    $prescription->checkIPC($alertesIPC, $_perf_line->code_cip);
	    $prescription->checkInteractions($alertesInteractions, $_perf_line->code_cip);
	    if(!$_perf_line->_ref_produit->inLivret){
	      $prescription->_scores["hors_livret"]++;
	    }
	  }
	}
}  


// Création du template
$smarty = new CSmartyDP();
$smarty->assign("prescription", $prescription);
$smarty->assign("now", mbDateTime());
$smarty->display("inc_vw_prescription_meds.tpl");

?>