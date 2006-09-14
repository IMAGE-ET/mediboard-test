<?php /* $Id: do_planning_aed.php 110 2006-06-11 20:19:38Z Rhum1 $ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision: 110 $
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

if ($praticien_id = mbGetValueFromPost("praticien_id")) {
  mbSetValueToSession("praticien_id", $praticien_id);
}

$do = new CDoObjectAddEdit("CSejour", "sejour_id");
$do->createMsg = "Séjour créé";
$do->modifyMsg = "Séjour modifié";
$do->deleteMsg = "Séjour supprimé";
$do->doIt();

?>