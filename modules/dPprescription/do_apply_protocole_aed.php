<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Alexis Granger
 */


global $AppUI, $can;

$can->needsRead();

$prescription_id = mbGetValueFromPost("prescription_id");
$protocole_id    = mbGetValueFromPost("protocole_id");
$date_sel  = mbGetValueFromPost("debut", mbDate());
$praticien_id    = mbGetValueFromPost("praticien_id", $AppUI->user_id);

if(!$protocole_id){
	exit();
}

// Chargement de la prescription
$prescription = new CPrescription();
$prescription->load($prescription_id);
$prescription->applyProtocole($protocole_id, $praticien_id, $date_sel);

// Lancement du refresh des lignes de la prescription
echo "<script type='text/javascript'>Prescription.reloadPrescSejour($prescription_id)</script>";
echo $AppUI->getMsg();
exit();   

?>