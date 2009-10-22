<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

if ($chir_id = mbGetValueFromPost("chir_id")) {
  mbSetValueToSession("chir_id", $chir_id);
}

$do = new CDoObjectAddEdit("CConsultAnesth", "consultation_anesth_id");
$do->doIt();

?>
