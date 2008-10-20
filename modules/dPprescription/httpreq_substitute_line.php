<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPprescription
* @version $Revision: $
* @author Alexis Granger
*/

global $AppUI;

$code_cip    = mbGetValueFromGet("code_cip");
$line_id     = mbGetValueFromGet("line_id");
$mode_pharma = mbGetValueFromGet("mode_pharma"); 
$mode_protocole = mbGetValueFromGet("mode_protocole");

$line = new CPrescriptionLineMedicament();
$line->load($line_id);

// Creation de la nouvelle ligne
$line->_id = "";
$line->code_cip = $code_cip;
$line->creator_id = $AppUI->user_id;
$line->accord_praticien = "";
$line->debut = mbDate();
$line->time_debut = mbTime();
$msg = $line->store();

$AppUI->displayMsg($msg, "CPrescriptionLineMedicament-msg-create");
    
// Sauvegarde de l'ancienne ligne
$old_line = new CPrescriptionLineMedicament();
$old_line->load($line_id);
$old_line->substitution_line_id = $line->_id;
$old_line->date_arret = mbDate();
$old_line->time_arret = mbTime();
$msg = $old_line->store();
$AppUI->displayMsg($msg, "CPrescriptionLineMedicament-msg-store");

// Le passage de la ligne au reload permet de realiser le testPharma (pre-cochage de la case "Accord du praticien")
if($mode_protocole || $mode_pharma){
  echo "<script type='text/javascript'>Prescription.reload($line->prescription_id, '', '', '$mode_protocole', '$mode_pharma','$line->_id')</script>";
} else {
  echo "<script type='text/javascript'>Prescription.reloadPrescSejour($line->prescription_id)</script>";
}
echo $AppUI->getMsg();
CApp::rip();
?>