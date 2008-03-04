<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPprescription
* @version $Revision: $
* @author Alexis Granger
*/

global $AppUI;

$do = new CDoObjectAddEdit("CElementPrescription", "element_prescription_id");
$do->createMsg = "Elément créé";
$do->modifyMsg = "Elément modifié";
$do->deleteMsg = "Elément supprimé";
$do->doIt();

?>