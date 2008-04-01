<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPprescription
* @version $Revision: $
* @author Alexis Granger
*/

global $AppUI;

$do = new CDoObjectAddEdit("CMomentUnitaire", "moment_unitaire_id");
$do->createMsg = "Moment unitaire créé";
$do->modifyMsg = "Moment unitaire modifié";
$do->deleteMsg = "Moment unitaire supprimé";
$do->doIt();

?>