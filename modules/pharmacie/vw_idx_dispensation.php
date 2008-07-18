<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage pharmacie
 *	@version $Revision: $
 *  @author Alexis Granger
 */
 

global $g;

// Chargement de la liste des services
$service = new CService();
$service->group_id = $g;
$services = $service->loadMatchingList();

// Calcul de date_max et date_min
$date = mbDate();

$date_min = "$date 00:00:00";
$date_max = "$date 23:59:00";

/*
$date_min = mbDateTime("00:00:00", $date);
$date_max = mbDateTime("23:59:59", $date);
*/

$service_id = 3;

// Recherche des prescriptions dont les dates de sejours correspondent
$where = array();

$ljoin["sejour"] = "prescription.object_id = sejour.sejour_id";
$ljoin["affectation"] = "sejour.sejour_id = affectation.sejour_id";
$ljoin["lit"] = "affectation.lit_id = lit.lit_id";
$ljoin["chambre"] = "lit.chambre_id = chambre.chambre_id";
$ljoin["service"] = "chambre.service_id = service.service_id";
$where["prescription.type"] = " = 'sejour'";
$where[] = "(sejour.entree_prevue BETWEEN '$date_min' AND '$date_max') OR 
	 				  (sejour.sortie_prevue BETWEEN '$date_min' AND '$date_max') OR
				    (sejour.entree_prevue <= '$date_min' AND sejour.sortie_prevue >= '$date_max')";	
$where["service.service_id"] = " = '$service_id'";

$dispensations = array();
$prescriptions = array();
$medicaments = array();
$quantites_traduites = array();

$prescription = new CPrescription();
$prescriptions = $prescription->loadList($where, null, null, null, $ljoin);
foreach($prescriptions as $_prescription){
	$_prescription->loadRefsLinesMed("1","1");

  // Stockage du sejour de la prescription
	$sejour =& $_prescription->_ref_object;
		
	// On borne les dates aux dates du sejour si besoin
	$date_min = ($date_min < $sejour->_entree) ? $sejour->_entree : $date_min;
	$date_max = ($date_max > $sejour->_sortie) ? $sejour->_sortie : $date_max;
			
	foreach($_prescription->_ref_prescription_lines as $_line_med){	
		$_line_med->_ref_produit->loadConditionnement();
		// On remplit les bornes de la ligne avec les dates du sejour si besoin
		$_line_med->_debut_reel = (!$_line_med->_debut_reel) ? $sejour->_entree : $_line_med->_debut_reel;
		$_line_med->_fin_reelle = (!$_line_med->_fin_reelle) ? $sejour->_sortie : $_line_med->_fin_reelle;
		
		// Si la ligne n'est pas dans les bornes donné, on en tient pas compte
		if (!($_line_med->_debut_reel >= $date_min && $_line_med->_debut_reel <= $date_max ||
		    $_line_med->_fin_reelle >= $date_min && $_line_med->_fin_reelle <= $date_max ||
		    $_line_med->_debut_reel <= $date_min && $_line_med->_fin_reelle >= $date_max)){
		  continue;    	
		}
		
		// Calcul de la quantite en fonction des prises
		$_line_med->calculQuantiteLine($date_min, $date_max);
		foreach($_line_med->_quantites as $unite_prise => $quantite){
      $_unite_prise = str_replace("/kg", "", $unite_prise);
  		// Dans le cas d'un unite_prise/kg
  		if($_unite_prise != $unite_prise){
  			// On recupere le poids du patient pour calculer la quantite
				if(!$_prescription->_ref_object->_ref_patient){
	  			$_prescription->_ref_object->loadRefPatient();
	  		}
				$patient =& $_prescription->_ref_object->_ref_patient;
	  		if(!$patient->_ref_constantes_medicales){
				  $patient->loadRefConstantesMedicales();
	  		}
	      $const_med = $patient->_ref_constantes_medicales;
	      $poids     = $const_med->poids;
	      $quantite  = $quantite * $poids;
  		}
		  @$dispensations[$_line_med->code_cip][$_unite_prise] += $quantite;	
		}
		if(!array_key_exists($_line_med->code_cip, $medicaments)){
			$medicaments[$_line_med->code_cip] =& $_line_med->_ref_produit;
		}
	}
	
	// Calcul du nombre de boites (unites de presentation)
  foreach($dispensations as $code_cip => $unites){
  	$medicament =& $medicaments[$code_cip];	
  	foreach($unites as $unite_prise => $quantite){		
  		$coef = @$medicament->rapport_unite_prise[$unite_prise][$medicament->libelle_unite_presentation];
  		if(!$coef){
  			$coef = 1;
  		}
			$_quantite = $quantite * $coef;
  		// Affichage des quantites traduites en fonction de l'unite de reference
  		if($_quantite != $quantite){
  			@$quantites_traduites[$code_cip][$unite_prise] += $_quantite;
  		}
  		$presentation = $_quantite/$medicament->nb_unite_presentation;
  		$_presentation = $presentation/$medicament->nb_presentation;
  		@$quantites[$code_cip] += $_presentation;
  	}	
  }
}

// On arrondit la quantite de "boites"
foreach($quantites as &$_quantite){
  $explode_nb = explode(".", $_quantite);
  if(count($explode_nb)>1){
  	$_quantite = ceil($_quantite);
  }
}


// Smarty template
$smarty = new CSmartyDP();
$smarty->assign("dispensations", $dispensations);
$smarty->assign("medicaments"  , $medicaments);
$smarty->assign("quantites", $quantites);
$smarty->assign("quantites_traduites", $quantites_traduites);
$smarty->display('vw_idx_dispensation.tpl');


?>