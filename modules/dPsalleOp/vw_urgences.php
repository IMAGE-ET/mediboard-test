<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPsalleOp
* @version $Revision: 331 $
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m, $g;

if (!$canRead) {
  $AppUI->redirect("m=system&a=access_denied");
}

$date  = mbGetValueFromGetOrSession("date", mbDate());

// Listes des urgences
$operation = new COperation;
$where = array (
  "date" => "= '$date'",
);
$order = "salle_id, chir_id";
$urgences = $operation->loadList($where, $order);
foreach ($urgences as &$urgence) {
  $urgence->loadRefsFwd();
  $urgence->_ref_sejour->loadRefPatient();
}

// Listes des salles
$salle = new CSalle;
$where = array (
  "group_id" => "= '$g'",
);
$order = "nom";

$listSalles = $salle->loadList($where, $order);

// Création du template
$smarty = new CSmartyDP();

$smarty->debugging = false;

$smarty->assign("urgences"  , $urgences);
$smarty->assign("listSalles", $listSalles);
$smarty->assign("date",$date);

$smarty->display("vw_urgences.tpl");

?>