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
$services = $service->loadGroupList();

// Recuperation des valeurs
$praticien_id  = mbGetValueFromGet("praticien_id");
$service_id    = mbGetValueFromGet("service_id");
$valide_pharma = mbGetValueFromGet("valide_pharma", 0);  // Par defaut, seulement les prescriptions contenant des lignes non validees

$date = mbDate();
$filter_sejour = new CSejour();
$filter_sejour->_date_entree = mbGetValueFromGet('_date_entree', mbGetValueFromSession('_date_min'), $date);
$filter_sejour->_date_sortie = mbGetValueFromGet('_date_sortie', mbGetValueFromSession('_date_max'), $date);

mbSetValueToSession('_date_min', $filter_sejour->_date_entree);
mbSetValueToSession('_date_max', $filter_sejour->_date_sortie);

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
$min = "$filter_sejour->_date_entree 00:00:00";
$max = "$filter_sejour->_date_sortie 23:59:59";
if($service_id){
  $ljoin["affectation"] = "sejour.sejour_id = affectation.sejour_id";
  $ljoin["lit"]         = "affectation.lit_id = lit.lit_id";
  $ljoin["chambre"]     = "lit.chambre_id = chambre.chambre_id";
  $ljoin["service"]     = "chambre.service_id = service.service_id";

  // Recupération de l'affectation courante
  $where[] = "(affectation.entree BETWEEN '$min' AND '$max') OR 
		 				  (affectation.sortie BETWEEN '$min' AND '$max') OR
					    (affectation.entree <= '$min' AND affectation.sortie >= '$max')";
  $where["service.service_id"] = " = '$service_id'";
} else {
	// Filtre sur les dates du séjour
	$where[] = "(sejour.entree_prevue BETWEEN '$min' AND '$max') OR 
							(sejour.sortie_prevue BETWEEN '$min' AND '$max') OR
						  (sejour.entree_prevue <= '$min' AND sejour.sortie_prevue >= '$max')";
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