<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPsalleOp
* @version $Revision: 331 $
* @author Romain Ollivier
*/

global $AppUI, $can, $m, $g;

$can->needsRead();

$date  = mbGetValueFromGetOrSession("date", mbDate());

// Listes des urgences
$operation = new COperation;
$where = array (
  "date" => "= '$date'",
);
$order = "salle_id, chir_id";
$urgences = $operation->loadGroupList($where, $order);
foreach ($urgences as &$urgence) {
  $urgence->loadRefsFwd();
  $urgence->_ref_sejour->loadRefPatient();
}

// Listes des salles
$salle = new CSalle;
$order = "nom";

$listSalles = $salle->loadGroupList(array(), $order);

// Création du template
$smarty = new CSmartyDP();

$smarty->debugging = false;

$smarty->assign("urgences"  , $urgences);
$smarty->assign("listSalles", $listSalles);
$smarty->assign("date",$date);

$smarty->display("../../dPsalleOp/templates/vw_urgences.tpl");

?>