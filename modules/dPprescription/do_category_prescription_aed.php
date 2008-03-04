<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPprescription
* @version $Revision: $
* @author Alexis Granger
*/

global $AppUI;

$do = new CDoObjectAddEdit("CCategoryPrescription", "category_prescription_id");
$do->createMsg = "Catégorie créée";
$do->modifyMsg = "Catégorie modifiée";
$do->deleteMsg = "Catégorie supprimée";
$do->doIt();

?>