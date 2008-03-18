<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPprescription
* @version $Revision: $
* @author Alexis Granger
*/

global $AppUI;

$do = new CDoObjectAddEdit("CPrisePosologie", "prise_posologie_id");
$do->createMsg = "Posologie ajouté";
$do->modifyMsg = "Posologie modifié";
$do->deleteMsg = "Posologie supprimé";
$do->doIt();

?>