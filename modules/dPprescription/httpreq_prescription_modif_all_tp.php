<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPprescription
* @version $Revision: $
* @author Alexis Granger
*/

global $AppUI;

$prescription_id = mbGetValueFromGet("prescription_id");
$praticien_id    = mbGetValueFromGet("praticien_id", $AppUI->user_id);
$date            = mbGetValueFromGet("date", mbDate());
$actionType      = mbGetValueFromGet("actionType", "stop");
$mode_pharma     = mbGetValueFromGet("mode_pharma");

// Chargement de la prescription 
$prescription = new CPrescription();
$prescription->load($prescription_id);
$prescription->loadRefObject();
$prescription->_ref_object->loadRefPrescriptionTraitement();
$prescription_traitement =& $prescription->_ref_object->_ref_prescription_traitement;
$prescription_traitement->loadRefsLines();

foreach($prescription_traitement->_ref_prescription_lines as &$line) {
	if($actionType == "stop" && !$line->date_arret) {
		$line->date_arret = $date;
    $AppUI->displayMsg($line->store(), "msg-CPrescriptionLineMedicament-store");
	}
	if($actionType == "go" && $line->date_arret) {
		$line->duplicateLine($praticien_id, $prescription_id);
		
	}
}

echo "<script type='text/javascript'>Prescription.reload($prescription->_id, '', '', '', '$mode_pharma')</script>";
echo $AppUI->getMsg();
exit();

?>