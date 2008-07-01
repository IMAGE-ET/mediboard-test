<?php

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision:
* @author Alexis Granger
*/

global $AppUI, $can, $m, $g;
CAppUI::requireModuleFile("dPhospi", "inc_vw_affectations");

$can->needsRead();

// Filtres
$date = mbGetValueFromGetOrSession("date", mbDate());
$datetime = mbDateTime(); 
$mode = mbGetValueFromGetOrSession("mode", 0);
$service_id   = mbGetValueFromGetOrSession("service_id");
$praticien_id = mbGetValueFromGetOrSession("praticien_id");

// Chargement de l'utilisateur courant
$userCourant = new CMediusers;
$userCourant->load($AppUI->user_id);

$prescription_sejour = new CPrescription();

// Test du type de l'utilisateur courant

$anesthesiste = $userCourant->isFromType(array("Anesthésiste"));
$praticien    = $userCourant->isPraticien();

if($praticien && !$service_id && !$praticien_id) {
  $praticien_id = $userCourant->user_id;
}


$changeSejour = mbGetValueFromGet("service_id") || mbGetValueFromGet("praticien_id") || mbGetValueFromGet("date");
$changeSejour = $changeSejour || (!$service_id && !$praticien_id);

if($changeSejour) {
  $sejour_id = null;
  mbSetValueToSession("sejour_id");
} else {
  $sejour_id = mbGetValueFromGetOrSession("sejour_id");
}


// Récupération du service à ajouter/éditer
$totalLits = 0;

// A passer en variable de configuration
$heureLimit = "16:00:00";

// Initialisation
$service = new CService;
$groupSejourNonAffectes = array();
$sejoursParService = array();

// Chargement de la liste de praticiens
$prat = new CMediusers();
$praticiens = $prat->loadPraticiens(PERM_READ);


// Si seulement le praticien est indiqué
if($praticien_id && !$service_id){
	$sejours = array();
	$sejour = new CSejour();
	$where["praticien_id"] = " = '$praticien_id'";
	$where["entree_prevue"] = " <= '$date 23:59:59'";
	$where["sortie_prevue"] = " >= '$date 00:00:00'";
	$where["annule"] = " = '0'";
	$where[] = "type != 'urg' AND type != 'exte'";
	
	$sejours = $sejour->loadList($where);
	foreach($sejours as &$_sejour){
		$_sejour->loadRefsPrescriptions();

		if($_sejour->_ref_prescriptions){
		  if(array_key_exists('sejour', $_sejour->_ref_prescriptions)){
			   $prescription_sejour =& $_sejour->_ref_prescriptions["sejour"];
			   $prescription_sejour->countNoValideLines();
			}
		}
	  
		$_sejour->loadRefPatient();
		$_sejour->loadRefPraticien();
		$_sejour->_ref_praticien->loadRefFunction();
		$_sejour->loadNumDossier();

		// Recherche de toutes les affectations pour la journee courante
		$affectations = array();
		$affectation = new CAffectation();
		$where = array();
  	$where["sejour_id"] = " = '$_sejour->_id'";
		$where["entree"] = "<= '$date 23:59:59'";
    $where["sortie"] = ">= '$date 00:00:00'";
    $affectations = $affectation->loadList($where);

    if(count($affectations) >= 1){
	    foreach($affectations as &$_affectation){
	    	$_affectation->loadRefLit();
    	  $_affectation->_ref_lit->loadCompleteView();
		  	// Cache de services
				$_service_id = $_affectation->_ref_lit->_ref_chambre->service_id;
				if(!array_key_exists($_service_id, $sejoursParService)) {
		  		$_service = new CService();
		  		$_service->load($_service_id);
		  		$sejoursParService[$_service->_id] = $_service;	
		  	} 
	  		$service =& $sejoursParService[$_service_id];
		  	$chambre =& $_affectation->_ref_lit->_ref_chambre;
		  	$lit =& $_affectation->_ref_lit;
		  	$affectation =& $_affectation;
		  	$affectation->_ref_sejour =& $_sejour;
		  	$affectation->_ref_sejour->loadRefPraticien();
		  	$affectation->_ref_sejour->_ref_praticien->loadRefFunction();
		  	$service->_ref_chambres[$chambre->_id] = $chambre;
		  	$service->_ref_chambres[$chambre->_id]->_ref_lits[$lit->_id] = $lit;
				$service->_ref_chambres[$chambre->_id]->_ref_lits[$lit->_id]->_ref_affectations[$affectation->_id] = $affectation; 
		  }
    } else {
		  $sejoursParService["NP"][$_sejour->_id] = $_sejour;
	  }
	}
}

// Tri des sejours par services
ksort($sejoursParService);

// Récuperation du sejour sélectionné
$sejour = new CSejour;
$sejour->load($sejour_id);
$sejour->loadRefs();
$sejour->loadRefsPrescriptions();
$sejour->loadRefsDocs();


// Récupération de la liste des services
$where = array();
$where["group_id"] = "= '$g'";
$services = new CService;
$order = "nom";
if($praticien_id) {
  $services = $services->loadList($where, $order);
} else {
  $services = $services->loadListWithPerms(PERM_READ, $where, $order);
}


if($service_id){
	// Chargement des séjours à afficher
	if($service_id == "NP") {

		// Liste des patients à placer
	  $order = "entree_prevue ASC";
		  
	  // Admissions de la veille
	  $dayBefore = mbDate("-1 days", $date);
	  $where = array(
		  "entree_prevue" => "BETWEEN '$dayBefore 00:00:00' AND '$date 00:00:00'",
		  "type" => "!= 'exte'",
		  "annule" => "= '0'"
		);
		  
		$groupSejourNonAffectes["veille"] = loadSejourNonAffectes($where, $order, $praticien_id);
		  
		// Admissions du matin
		$where = array(
		  "entree_prevue" => "BETWEEN '$date 00:00:00' AND '$date ".mbTime("-1 second",$heureLimit)."'",
		  "type" => "!= 'exte'",
		  "annule" => "= '0'"
		);
		  
		$groupSejourNonAffectes["matin"] = loadSejourNonAffectes($where, $order, $praticien_id);
		  
		// Admissions du soir
		$where = array(
		  "entree_prevue" => "BETWEEN '$date $heureLimit' AND '$date 23:59:59'",
		  "type" => "!= 'exte'",
		  "annule" => "= '0'"
		);
		  
		$groupSejourNonAffectes["soir"] = loadSejourNonAffectes($where, $order, $praticien_id);
		  
		// Admissions antérieures
		$twoDaysBefore = mbDate("-2 days", $date);
		$where = array(
		  "entree_prevue" => "<= '$twoDaysBefore 23:59:59'",
		  "sortie_prevue" => ">= '$date 00:00:00'",
		  //"'$twoDaysBefore' BETWEEN entree_prevue AND sortie_prevue",
		  "annule" => "= '0'",
		  "type" => "!= 'exte'"
	  );
		  
		$groupSejourNonAffectes["avant"] = loadSejourNonAffectes($where, $order, $praticien_id);
	  
	} else {
	  $service->load($service_id);
	  loadServiceComplet($service, $date, $mode, $praticien_id);
	}
	
	if($service->_id){
		foreach($service->_ref_chambres as &$_chambre){
			foreach($_chambre->_ref_lits as &$_lits){
				foreach($_lits->_ref_affectations as &$_affectation){
					$_affectation->_ref_sejour->loadRefsPrescriptions();
					if($_affectation->_ref_sejour->_ref_prescriptions){
						if(array_key_exists('sejour', $_affectation->_ref_sejour->_ref_prescriptions)){
						  $prescription_sejour =& $_affectation->_ref_sejour->_ref_prescriptions["sejour"];
							$prescription_sejour->countNoValideLines();
						}
					}
				}
			}
		}
	}
	$sejoursParService[$service->_id] = $service;
}



// Création du template
$smarty = new CSmartyDP();

$smarty->assign("praticien"              , $praticien);
$smarty->assign("anesthesiste"           , $anesthesiste);
$smarty->assign("praticiens"             , $praticiens);
$smarty->assign("praticien_id"           , $praticien_id);
$smarty->assign("object"                 , $sejour);
$smarty->assign("mode"                   , $mode);
$smarty->assign("totalLits"              , $totalLits);
$smarty->assign("date"                   , $date);
$smarty->assign("isPrescriptionInstalled", CModule::getActive("dPprescription"));
$smarty->assign("isImedsInstalled"       , CModule::getActive("dPImeds"));
$smarty->assign("demain"                 , mbDate("+ 1 day", $date));
$smarty->assign("services"               , $services);
$smarty->assign("sejoursParService"      , $sejoursParService);
$smarty->assign("prescription_sejour"    , $prescription_sejour);
$smarty->assign("service_id"             , $service_id);
$smarty->assign("groupSejourNonAffectes" , $groupSejourNonAffectes);
$smarty->display("vw_idx_sejour.tpl");


?>