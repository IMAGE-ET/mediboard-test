<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Alexis Granger
 */


function viewMsg($msg, $action){
  global $AppUI, $m, $tab;
  $action = $AppUI->_($action);
  if($msg){
    $AppUI->setMsg("$action: $msg", UI_MSG_ERROR );
  }
  $AppUI->setMsg("$action", UI_MSG_OK );
}


global $AppUI, $can, $m;

$can->needsRead();

$prescription_line_id = mbGetValueFromPost("prescription_line_id");

// Chargement de la ligne de prescription
$prescription_line = new CPrescriptionLineMedicament();
$prescription_line->load($prescription_line_id);

$new_prescription_line = $prescription_line;

$new_prescription_line->_id = "";

// Si date_arret (cas du sejour)
if($prescription_line->date_arret){
	$new_prescription_line->debut = $prescription_line->date_arret;
  $new_prescription_line->duree = mbDaysRelative($new_prescription_line->debut,$new_prescription_line->_fin);
} else {
	// Sinon, on met la fin de la ligne initiale ou la date courante
	if($prescription_line->_fin){
		$new_prescription_line->debut = $prescription_line->_fin;
	} else {
	  $new_prescription_line->debut = mbDate();
	}
}

$new_prescription_line->no_poso = "";
$new_prescription_line->date_arret = "";

$new_prescription_line->unite_duree = "jour";
if($new_prescription_line->duree < 0){
	$new_prescription_line->duree = "";
}
$new_prescription_line->praticien_id = $AppUI->user_id;
$new_prescription_line->valide = 0;
$msg = $new_prescription_line->store();
viewMsg($msg, "msg-CPrescriptionLineMedicament-create");

// Lancement du refresh des lignes de la prescription
echo "<script type='text/javascript'>Prescription.reload($new_prescription_line->prescription_id,'','medicament')</script>";
echo $AppUI->getMsg();
exit();   

?>