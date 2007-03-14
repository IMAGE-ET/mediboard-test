<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage system
* @version $Revision: $
* @author Romain Ollivier
*/

$do = new CDoObjectAddEdit("CNote", "note_id");
$do->createMsg = "Note créée";
$do->modifyMsg = "Note modifiée";
$do->deleteMsg = "Note supprimée";
$do->doIt();
?>