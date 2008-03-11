<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPprescription
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI;

$do = new CDoObjectAddEdit("CPrescriptionLineMedicament", "prescription_line_id");
$do->createMsg = "Traitement ajouté";
$do->modifyMsg = "Traitement modifié";
$do->deleteMsg = "Traitement supprimé";
$do->doIt();

?>