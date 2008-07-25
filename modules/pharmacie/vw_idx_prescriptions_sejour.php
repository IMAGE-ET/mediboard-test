<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage pharmacie
 *	@version $Revision: $
 *  @author Alexis Granger
 */
 
global $AppUI, $can, $m, $g;
 
$can->needsRead();

// Chargement de la liste des praticiens
$mediuser = new CMediusers();
$praticiens = $mediuser->loadPraticiens();

// Chargement de la liste des services
$service = new CService();
$service->group_id = $g;
$services = $service->loadMatchingList();

$filter_sejour = new CSejour();

// Recuperation des valeurs
$praticien_id = mbGetValueFromGet("praticien_id");
$service_id   = mbGetValueFromGet("service_id");
$valide_pharma = mbGetValueFromGet("valide_pharma", 0);  // Par defaut, seulement les prescriptions contenant des lignes non validees

$filter_sejour->_date_min     = mbGetValueFromGetOrSession("_date_min");
$filter_sejour->_date_max     = mbGetValueFromGetOrSession("_date_max");
mbSetValueToSession('_date_min', $filter_sejour->_date_min);
mbSetValueToSession('_date_max', $filter_sejour->_date_max);

// Si aucune date n'est specifiée, on filtre par rapport à la date d'aujourd'hui
if(!$filter_sejour->_date_min && !$filter_sejour->_date_max){
	$now = mbDate();
	$filter_sejour->_date_min = mbDateTime("- 7 DAYS", $now);
	$filter_sejour->_date_max = mbDateTime("+ 7 DAYS", $now);
}


// Initialisations
$lines_medicament = array();
$where = array();


$ljoinMedicament["prescription"] = "prescription_line_medicament.prescription_id = prescription.prescription_id";
$ljoinMedicament["sejour"] = "prescription.object_id = sejour.sejour_id";
	

$where["prescription.type"] = " = 'sejour'";

if($valide_pharma == 0){
  $where["valide_pharma"] = " = '$valide_pharma'";
}

// Filtre sur le praticiens (lignes)
if($praticien_id){
	$where["prescription_line_medicament.praticien_id"] = " = '$praticien_id'";
}

// Filtre sur le service, date des affectations
if($service_id){
  $ljoinMedicament["affectation"] = "sejour.sejour_id = affectation.sejour_id";
  $ljoinMedicament["lit"] = "affectation.lit_id = lit.lit_id";
  $ljoinMedicament["chambre"] = "lit.chambre_id = chambre.chambre_id";
  $ljoinMedicament["service"] = "chambre.service_id = service.service_id";
  // Recupération de l'affectation courante
  if($filter_sejour->_date_min && $filter_sejour->_date_max){
	  $where[] = "(affectation.entree BETWEEN '$filter_sejour->_date_min' AND '$filter_sejour->_date_max') OR 
			 				  (affectation.sortie BETWEEN '$filter_sejour->_date_min' AND '$filter_sejour->_date_max') OR
						    (affectation.entree <= '$filter_sejour->_date_min' AND affectation.sortie >= '$filter_sejour->_date_max')";
  } 
  $where["service.service_id"] = " = '$service_id'";
} else {
	// Filtre sur les dates du séjour
	if($filter_sejour->_date_min && $filter_sejour->_date_max){
		$where[] = "(sejour.entree_prevue BETWEEN '$filter_sejour->_date_min' AND '$filter_sejour->_date_max') OR 
								(sejour.sortie_prevue BETWEEN '$filter_sejour->_date_min' AND '$filter_sejour->_date_max') OR
							  (sejour.entree_prevue <= '$filter_sejour->_date_min' AND sejour.sortie_prevue >= '$filter_sejour->_date_max')";
	}
}

if($praticien_id || $service_id || ($filter_sejour->_date_min && $filter_sejour->_date_max)){
  $line_medicament = new CPrescriptionLineMedicament();
  $lines_medicament = $line_medicament->loadList($where, null, "100", null, $ljoinMedicament);
}

$prescriptions = array();

// Chargement de toutes les prescriptions
foreach($lines_medicament as $line_med){
	if(!array_key_exists($line_med->prescription_id, $prescriptions)){
	  $prescription = new CPrescription();
	  $prescription->load($line_med->prescription_id);
	  $prescription->loadRefPraticien();
	  $prescription->loadRefsLinesMedComments();
    $prescription->loadRefsLinesElementsComments();
    $prescriptions[$line_med->prescription_id] = $prescription;
	}
}



// Smarty template
$smarty = new CSmartyDP();

$smarty->assign("valide_pharma", $valide_pharma);
$smarty->assign("today", mbDate());
$smarty->assign("mode_pharma", "1");
$smarty->assign("prescription", new CPrescription());
$smarty->assign("filter_sejour", $filter_sejour);
$smarty->assign("filter_line_med", new CPrescriptionLineMedicament());
$smarty->assign("prescriptions", $prescriptions);
$smarty->assign("contexteType", "");
$smarty->assign("praticiens", $praticiens);
$smarty->assign("services", $services);
$smarty->assign("praticien_id", $praticien_id);
$smarty->assign("service_id", $service_id);

$smarty->display('vw_idx_prescriptions_sejour.tpl');

?>