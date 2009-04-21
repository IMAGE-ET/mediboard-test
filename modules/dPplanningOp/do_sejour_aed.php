<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Romain Ollivier
*/

if ($praticien_id = mbGetValueFromPost("praticien_id")) {
  mbSetValueToSession("praticien_id", $praticien_id);
}

$do = new CDoObjectAddEdit("CSejour", "sejour_id");
$do->doIt();

?>