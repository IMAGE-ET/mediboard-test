<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage pharmacie
 *	@version $Revision: $
 *  @author Alexis Granger
 */
 
global $can, $g;
$can->needsRead();

// Chargement de la liste des praticiens
$mediuser = new CMediusers();
$praticiens = $mediuser->loadPraticiens();

// Chargement de la liste des services
$service = new CService();
$list_services = $service->loadGroupList();

// Recuperation des valeurs
$praticien_id  = mbGetValueFromGet("praticien_id");
$service_id    = mbGetValueFromGet("service_id");
$valide_pharma = mbGetValueFromGet("valide_pharma", 0);  // Par defaut, seulement les prescriptions contenant des lignes non validees

$date = mbDate();
$filter_sejour = new CSejour();
$filter_sejour->_date_min = mbGetValueFromGetOrSession('_date_min', $date.' 00:00:00');
$filter_sejour->_date_max = mbGetValueFromGetOrSession('_date_max', $date.' 23:59:59');

mbSetValueToSession('_date_min', $filter_sejour->_date_min);
mbSetValueToSession('_date_max', $filter_sejour->_date_max);

// Initialisations
$lines_medicament = array();
$where = array();
$ljoin = array();

$ljoin["prescription_line_medicament"] = "prescription_line_medicament.prescription_id = prescription.prescription_id";
$ljoin["perfusion"] = "perfusion.prescription_id = prescription.prescription_id";
$ljoin["sejour"] = "prescription.object_id = sejour.sejour_id";
	
$where["prescription.type"] = " = 'sejour'";
if($valide_pharma == 0){
  $where[] = "prescription_line_medicament.valide_pharma != '1' OR perfusion.signature_pharma != '1'";
}

// Filtre sur le praticiens (lignes)
if($praticien_id){
	$where[] = " prescription_line_medicament.praticien_id = '$praticien_id' OR perfusion.praticien_id = '$praticien_id'";
}

// Filtre sur le service, date des affectations
if($service_id){
  $ljoin["affectation"] = "sejour.sejour_id = affectation.sejour_id";
  $ljoin["lit"]         = "affectation.lit_id = lit.lit_id";
  $ljoin["chambre"]     = "lit.chambre_id = chambre.chambre_id";
  $ljoin["service"]     = "chambre.service_id = service.service_id";
  
  // Recupération de l'affectation courante
  $where[] = "(affectation.entree BETWEEN '$filter_sejour->_date_min' AND '$filter_sejour->_date_max') OR 
		 				  (affectation.sortie BETWEEN '$filter_sejour->_date_min' AND '$filter_sejour->_date_max') OR
					    (affectation.entree <= '$filter_sejour->_date_min' AND affectation.sortie >= '$filter_sejour->_date_max')";
  $where["service.service_id"] = " = '$service_id'";
} else {
	// Filtre sur les dates du séjour
	$where[] = "(sejour.entree_prevue BETWEEN '$filter_sejour->_date_min' AND '$filter_sejour->_date_max') OR 
							(sejour.sortie_prevue BETWEEN '$filter_sejour->_date_min' AND '$filter_sejour->_date_max') OR
						  (sejour.entree_prevue <= '$filter_sejour->_date_min' AND sejour.sortie_prevue >= '$filter_sejour->_date_max')";
}

$prescriptions = new CPrescription();
$prescriptions = $prescriptions->loadList($where, null, 100, null, $ljoin);

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
$smarty->assign("mode_pack", "0");
$smarty->display('vw_idx_prescriptions_sejour.tpl');

?>