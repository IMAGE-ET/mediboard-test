<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage pharmacie
 *	@version $Revision: $
 *  @author Alexis Granger
 */
 
global $AppUI, $can, $m;

$can->needsRead();

$praticien_id = mbGetValueFromGet("praticien_id");

$ljoinMedicament["prescription"] = "prescription_line.prescription_id = prescription.prescription_id";
$ljoinElement["prescription"] = "prescription_line_element.prescription_id = prescription.prescription_id";
$ljoinComment["prescription"] = "prescription_line_comment.prescription_id = prescription.prescription_id";

if($praticien_id){
	$where["praticien_id"] = " = '$praticien_id'";
}

$where = array();
$where["prescription.type"] = " = 'sejour'";
$where["valide_pharma"] = " = '0'";

// Recuperation de toutes les lignes de medicaments de type sejour qui ne sont pas encore validées par le pharmacien
$line_medicament = new CPrescriptionLineMedicament();
$lines_medicament = $line_medicament->loadList($where, null, null, null, $ljoinMedicament);

$prescriptions = array();

// Chargement de toutes les prescriptions
foreach($lines_medicament as $line_med){
	if(!array_key_exists($line_med->prescription_id, $prescriptions)){
	  $prescription = new CPrescription();
	  $prescription->load($line_med->prescription_id);
	  $prescription->loadRefsLinesMedComments();
    $prescription->loadRefsLinesElementsComments();
    $prescriptions[$line_med->prescription_id] = $prescription;
	}
}

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign("prescription", new CPrescription());
$smarty->assign("prescriptions", $prescriptions);

$smarty->display('vw_idx_prescriptions_sejour.tpl');

?>