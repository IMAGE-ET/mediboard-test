<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPprescription
* @version $Revision: $
* @author Alexis Granger
*/

global $AppUI;

$do = new CDoObjectAddEdit("CExecutantPrescriptionLine", "executant_prescription_line_id");
$do->createMsg = "Exécutant créé";
$do->modifyMsg = "Exécutant modifié";
$do->deleteMsg = "Exécutant supprimé";
$do->doIt();

?>