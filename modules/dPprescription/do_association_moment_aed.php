<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPprescription
* @version $Revision: $
* @author Alexis Granger
*/

global $AppUI;

$do = new CDoObjectAddEdit("CAssociationMoment", "association_moment_id");
$do->createMsg = "Association créée";
$do->modifyMsg = "Association modifiée";
$do->deleteMsg = "Association supprimée";
$do->doIt();

?>