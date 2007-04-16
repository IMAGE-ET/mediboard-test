<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI;

$do = new CDoObjectAddEdit("CPrescriptionLabo", "prescription_labo_id");
$do->createMsg = "Prescription ajoutée";
$do->modifyMsg = "Prescription modifiée";
$do->deleteMsg = "Prescription supprimée";
$do->doIt();

?>