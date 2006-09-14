<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI;

$do = new CDoObjectAddEdit("CFunctions", "function_id");
$do->createMsg = "Fonction créée";
$do->modifyMsg = "Fonction modifiée";
$do->deleteMsg = "Fonction supprimée";
$do->doIt();

?>