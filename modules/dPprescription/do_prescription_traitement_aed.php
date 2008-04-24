<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPprescription
* @version $Revision: $
* @author Alexis Granger
*/

$prescription_line_id = mbGetValueFromPost("prescription_line_id");
$prescription_id = mbGetValueFromPost("prescription_id");
$traitement = mbGetValueFromPost("_traitement");

// Chargement de la ligne de prescription
$line = new CPrescriptionLineMedicament();
$line->load($prescription_line_id);

if($traitement == 1){
	// Chargement de la prescription de type traitement
  $prescription = new CPrescription();
  $prescription->object_id = $line->_ref_prescription->object_id;
  $prescription->object_class = $line->_ref_prescription->object_class;
  $prescription->type = "traitement";
  $prescription->loadMatchingObject();
  if(!$prescription->_id){
  	// Si la prescription de traitement n'existe pas, on la crée
    $prescription->praticien_id = $AppUI->user_id;
  	$msg = $prescription->store();
  }
  $line->prescription_id = $prescription->_id;  
  // Suppression des valeurs non disponibles pour une ligne de type traitement
  $line->debut = "";
  $line->duree = "";
  $line->unite_duree = "";
  $line->ald = "";
  $line->date_arret = "";
  $msg = $line->store();
}


// On repasse la ligne en type normal (pre_admission, sejour, sortie)
if($traitement == 0){
	$line->praticien_id = $AppUI->user_id;
	$line->prescription_id = $prescription_id;
	$msg = $line->store();
}


// Lancement du refresh des lignes de la prescription
echo "<script type='text/javascript'>Prescription.reload($prescription_id,'','medicament')</script>";
echo $AppUI->getMsg();
exit();

?>