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
$urgences = $operation->loadGroupList($where, "salle_id, chir_id");
foreach ($urgences as &$urgence) {
  $urgence->loadRefsFwd();
  $urgence->_ref_sejour->loadRefPatient();
}

// Listes des salles
$listBlocs = CGroups::loadCurrent()->loadBlocs(PERM_READ);

$salle = new CSalle();
$listSalles = $salle->loadListWithPerms(PERM_READ);

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->debugging = false;

$smarty->assign("urgences"  , $urgences);
$smarty->assign("listBlocs",  $listBlocs);
$smarty->assign("listSalles", $listSalles);
$smarty->assign("date",$date);

$smarty->display("../../dPsalleOp/templates/vw_urgences.tpl");

?>