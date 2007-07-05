<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPlabo
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI;

$do = new CDoObjectAddEdit("CPrescriptionLaboExamen", "prescription_labo_examen_id");
$do->createMsg = "Analyse ajoutée";
$do->modifyMsg = "Analyse modifiée";
$do->deleteMsg = "Analyse enlevée";
$do->doIt();

?>