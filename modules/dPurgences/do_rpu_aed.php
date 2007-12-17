<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPurgences
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI;

$do = new CDoObjectAddEdit("CRPU", "rpu_id");
$do->createMsg = "Urgence créée";
$do->modifyMsg = "Urgence modifiée";
$do->deleteMsg = "Urgence supprimée";
$do->doIt();

?>