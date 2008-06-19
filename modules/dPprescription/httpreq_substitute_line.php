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

$line = new CPrescriptionLineMedicament();
$line->load($line_id);

// Creation de la nouvelle ligne
$line->_id = "";
$line->code_cip = $code_cip;
$line->creator_id = $AppUI->user_id;
$msg = $line->store();

$AppUI->displayMsg($msg, "msg-CPrescriptionLineMedicament-create");
    
// Sauvegarde de 
$old_line = new CPrescriptionLineMedicament();
$old_line->load($line_id);
$old_line->substitution_line_id = $line->_id;
$old_line->date_arret = mbDate();
$old_line->time_arret = mbTime();
$msg = $old_line->store();
$AppUI->displayMsg($msg, "msg-CPrescriptionLineMedicament-store");

echo "<script type='text/javascript'>Prescription.reload($line->prescription_id, '', '', '', '$mode_pharma')</script>";
echo $AppUI->getMsg();
exit();

?>