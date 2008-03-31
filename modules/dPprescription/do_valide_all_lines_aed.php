<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPprescription
* @version $Revision: $
* @author Alexis Granger
*/

function viewMsg($msg, $action){
  global $AppUI, $m, $tab;
  $action = $AppUI->_($action);
  if($msg){
    $AppUI->setMsg("$action: $msg", UI_MSG_ERROR );
  }
  $AppUI->setMsg("$action", UI_MSG_OK );
}


global $AppUI;

$prescription_id = mbGetValueFromPost("prescription_id");

// Chargement de toutes les lignes du user_courant non validées
$prescriptionLineMedicament = new CPrescriptionLineMedicament();
$prescriptionLineMedicament->prescription_id = $prescription_id;
$prescriptionLineMedicament->praticien_id = $AppUI->user_id;
$prescriptionLineMedicament->valide = "0";
$medicaments = $prescriptionLineMedicament->loadMatchingList();

foreach($medicaments as $key => $lineMedicament){
	$lineMedicament->valide = 1;
	$msg = $lineMedicament->store();
	viewMsg($msg, "msg-CPrescriptionLineMedicament-modify");
}

$AppUI->redirect("m=dPprescription&a=vw_edit_prescription&popup=1&dialog=1&prescription_id=".$prescription_id);

?>