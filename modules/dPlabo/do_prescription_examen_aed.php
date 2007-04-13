<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI;

$do = new CDoObjectAddEdit("CPrescriptionLaboExamen", "prescription_labo_examen_id");
$do->createMsg = "Examen ajouté";
$do->modifyMsg = "Examen modifié";
$do->deleteMsg = "Examen supprimé";
$do->doIt();

?>