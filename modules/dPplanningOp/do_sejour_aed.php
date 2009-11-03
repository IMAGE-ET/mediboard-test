<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Romain Ollivier
*/

if ($praticien_id = CValue::post("praticien_id")) {
  CValue::setSession("praticien_id", $praticien_id);
}

$do = new CDoObjectAddEdit("CSejour", "sejour_id");
$do->doIt();

?>