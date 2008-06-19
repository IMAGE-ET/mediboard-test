<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPprescription
* @version $Revision: $
* @author Alexis Granger
*/


global $AppUI;

$do = new CDoObjectAddEdit("CPrescriptionLineElement", "prescription_line_element_id");
$do->createMsg = "Elément ajouté";
$do->modifyMsg = "Elément modifié";
$do->deleteMsg = "Elément supprimé";
$do->doIt();

?>