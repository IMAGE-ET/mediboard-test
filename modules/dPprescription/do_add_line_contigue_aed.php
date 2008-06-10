<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Alexis Granger
 */


function viewMsg($msg, $action){
  global $AppUI, $m, $tab;
  $action = CAppUI::tr($action);
  if($msg){
    $AppUI->setMsg("$action: $msg", UI_MSG_ERROR );
  }
  $AppUI->setMsg("$action", UI_MSG_OK );
}


global $AppUI, $can, $m;

$can->needsRead();

$prescription_line_id = mbGetValueFromPost("prescription_line_id");
$prescription_id = mbGetValueFromPost("prescription_id");
$praticien_id = mbGetValueFromPost("praticien_id", $AppUI->user_id);

// Chargement de la prescription
$prescription = new CPrescription();
$prescription->load($prescription_id);

$mode_pharma = mbGetValueFromPost("mode_pharma");

// Chargement de la ligne de prescription
$new_line = new CPrescriptionLineMedicament();
$new_line->load($prescription_line_id);

$date_arret_tp = $new_line->date_arret;

$new_line->loadRefsPrises();
$new_line->loadRefPrescription();

$new_line->_id = "";

// Si date_arret (cas du sejour)
if($new_line->date_arret){
	$new_line->debut = $new_line->date_arret;
	$new_line->date_arret = "";
	if($new_line->date_arret < $new_line->_fin){
    $new_line->duree = mbDaysRelative($new_line->debut,$new_line->_fin);
	}
} else {
	// Sinon, on met la fin de la ligne initiale ou la date courante
	//f($new_line->_fin){
	//	$new_line->debut = $new_line->_fin;
	//} else {
	  $new_line->debut = mbDate();
	//}
}

$new_line->unite_duree = "jour";
if($new_line->duree < 0){
	$new_line->duree = "";
}
$new_line->praticien_id = $praticien_id;
$new_line->signee = 0;
$new_line->valide_pharma = 0;

// Si prescription de sortie, on duplique la ligne en ligne de prescription
if($prescription->type == "sortie" && $new_line->_traitement && !$date_arret_tp){
	$new_line->prescription_id = $prescription_id;
}


$msg = $new_line->store();

viewMsg($msg, "msg-CPrescriptionLineMedicament-create");

foreach($new_line->_ref_prises as &$prise){
	// On copie les prises
	$prise->_id = "";
	$prise->object_id = $new_line->_id;
	$prise->object_class = "CPrescriptionLineMedicament";
	$msg = $prise->store();
	viewMsg($msg, "msg-CPrisePosologie-create");
}

$old_line = new CPrescriptionLineMedicament();
$old_line->load($prescription_line_id);

if(!($prescription->type == "sortie" && $old_line->_traitement && !$date_arret_tp)){
	$old_line->child_id = $new_line->_id;
	if($prescription->type != "sortie" && !$old_line->date_arret){
	  $old_line->date_arret = mbDate();
	}
	$old_line->store();
}

echo "<script type='text/javascript'>Prescription.reload($prescription_id,'','medicament','','$mode_pharma')</script>";
echo $AppUI->getMsg();
exit();

?>