<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPprescription
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI;

$do = new CDoObjectAddEdit("CPrescription", "prescription_id");
$do->createMsg = "Prescription créée";
$do->modifyMsg = "Prescription modifiée";
$do->deleteMsg = "Prescription supprimée";
$do->doIt();

?>