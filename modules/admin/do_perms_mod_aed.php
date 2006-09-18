<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage admin
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI;

$do = new CDoObjectAddEdit("CPermModule", "perm_module_id");
$do->createMsg = "Permission créée";
$do->modifyMsg = "Permission modifiée";
$do->deleteMsg = "Permission supprimée";
$do->doIt();

?>