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

$mode_pharma = mbGetValueFromPost("mode_pharma");

// Chargement de la ligne de prescription
$new_line = new CPrescriptionLineMedicament();
$new_line->load($prescription_line_id);
$new_line->loadRefsPrises();

// On copie la ligne
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
	if($new_line->_fin){
		$new_line->debut = $new_line->_fin;
	} else {
	  $new_line->debut = mbDate();
	}
}

$new_line->unite_duree = "jour";
if($new_line->duree < 0){
	$new_line->duree = "";
}
$new_line->praticien_id = $AppUI->user_id;
$new_line->signee = 0;
$new_line->valide_pharma = 0;
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


// Lancement du refresh des lignes de la prescription
echo "<script type='text/javascript'>Prescription.reload($new_line->prescription_id,'','medicament','',$mode_pharma)</script>";
echo $AppUI->getMsg();
exit();

?>