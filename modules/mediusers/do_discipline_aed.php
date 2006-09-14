<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI;

$do = new CDoObjectAddEdit("CDiscipline", "discipline_id");
$do->createMsg = "Discipline créée";
$do->modifyMsg = "Discipline modifiée";
$do->deleteMsg = "Discipline supprimée";
$do->doIt();

?>