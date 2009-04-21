<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Romain Ollivier
*/

if ($chir_id = mbGetValueFromPost("chir_id")) {
  mbSetValueToSession("chir_id", $chir_id);
}

$do = new CDoObjectAddEdit("CProtocole", "protocole_id");
//$do->redirectDelete = "m=$m&tab=vw_edit_protocole&protocole_id=0";
$do->doIt();

?>