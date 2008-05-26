<?php

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision:
* @author Alexis Granger
*/

global $AppUI, $can, $m, $g;
require_once($AppUI->getModuleFile("dPhospi", "inc_vw_affectations"));

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

if($praticien) {
  $praticien_id = $userCourant->user_id;
}

if($praticien_id){
	$mode = 1;
	$service_id = "";
}

if($service_id){
	$praticien_id = "";
}

$sejour_id = mbGetValueFromGetOrSession("sejour_id",0);

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


if($praticien_id){
	$sejours = array();
	$sejour = new CSejour();
	$where["praticien_id"] = " = '$praticien_id'";
	$where["entree_prevue"] = " <= '$datetime'";
	$where["sortie_prevue"] = " >= '$datetime'";
	$where["annule"] = " = '0'";
	$where[] = "type != 'urg' AND type != 'exte'";
	
	$sejours = $sejour->loadList($where);
	foreach($sejours as &$_sejour){
		$_sejour->loadRefsPrescriptions();

		if($_sejour->_ref_prescriptions){
		  if(array_key_exists('sejour', $_sejour->_ref_prescriptions)){
		  	if(array_key_exists('0', $_sejour->_ref_prescriptions["sejour"])){
			    $prescription_sejour =& $_sejour->_ref_prescriptions["sejour"]["0"];
			    $prescription_sejour->countNoValideLines();
		  	}
			}
		}
	  $_sejour->loadCurrentAffectation($datetime);
		$_sejour->loadRefPatient();
		$_sejour->loadRefPraticien();
		$_sejour->_ref_praticien->loadRefFunction();
		$_sejour->loadNumDossier();
		
	  if ($_sejour->_ref_curr_affectation->_id){
			// Cache de services
			$_service_id = $_sejour->_ref_curr_affectation->_ref_lit->_ref_chambre->service_id;
			if(!array_key_exists($_service_id, $sejoursParService)) {
	  		$_service = new CService();
	  		$_service->load($_service_id);
	  		$sejoursParService[$_service->_id] = $_service;	
	  	} 
	  	
  		$service =& $sejoursParService[$_service_id];
	  	
	  	$chambre =& $_sejour->_ref_curr_affectation->_ref_lit->_ref_chambre;
	  	$lit =& $_sejour->_ref_curr_affectation->_ref_lit;
	  	$affectation =& $_sejour->_ref_curr_affectation;
	  	$affectation->_ref_sejour =& $_sejour;
	  	$affectation->_ref_sejour->loadRefPraticien();
	  	$affectation->_ref_sejour->_ref_praticien->loadRefFunction();
	  	// Ajout de la chambre dans le service
	  	$service->_ref_chambres[$chambre->_id] = $chambre;
	  	// Ajout du lit dans la chambre
	  	$service->_ref_chambres[$chambre->_id]->_ref_lits[$lit->_id] = $lit;
	  	// Ajout de l'affectation dans le lit
			$service->_ref_chambres[$chambre->_id]->_ref_lits[$lit->_id]->_ref_affectations[$affectation->_id] = $affectation;
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
		  
		$groupSejourNonAffectes["veille"] = loadSejourNonAffectes($where, $order);
		  
		// Admissions du matin
		$where = array(
		  "entree_prevue" => "BETWEEN '$date 00:00:00' AND '$date ".mbTime("-1 second",$heureLimit)."'",
		  "type" => "!= 'exte'",
		  "annule" => "= '0'"
		);
		  
		$groupSejourNonAffectes["matin"] = loadSejourNonAffectes($where, $order);
		  
		// Admissions du soir
		$where = array(
		  "entree_prevue" => "BETWEEN '$date $heureLimit' AND '$date 23:59:59'",
		  "type" => "!= 'exte'",
		  "annule" => "= '0'"
		);
		  
		$groupSejourNonAffectes["soir"] = loadSejourNonAffectes($where, $order);
		  
		// Admissions antérieures
		$twoDaysBefore = mbDate("-2 days", $date);
		$where = array(
		  "entree_prevue" => "<= '$twoDaysBefore 23:59:59'",
		  "sortie_prevue" => ">= '$date 00:00:00'",
		  //"'$twoDaysBefore' BETWEEN entree_prevue AND sortie_prevue",
		  "annule" => "= '0'",
		  "type" => "!= 'exte'"
	  );
		  
		$groupSejourNonAffectes["avant"] = loadSejourNonAffectes($where, $order);
	  
	} else {
	  $service->load($service_id);
	  loadServiceComplet($service, $date, $mode);
	}
	
	if($service->_id && !$praticien_id){
		foreach($service->_ref_chambres as &$_chambre){
			foreach($_chambre->_ref_lits as &$_lits){
				foreach($_lits->_ref_affectations as &$_affectation){
					$_affectation->_ref_sejour->loadRefsPrescriptions();
					if($_affectation->_ref_sejour->_ref_prescriptions){
						if(array_key_exists('sejour', $_affectation->_ref_sejour->_ref_prescriptions)){
							if(array_key_exists('0', $_sejour->_ref_prescriptions["sejour"])){
						    $prescription_sejour =& $_affectation->_ref_sejour->_ref_prescriptions["sejour"]["0"];
							  $prescription_sejour->countNoValideLines();
							}
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