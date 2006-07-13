<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPprotocoles
* @version $Revision: 331 $
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
  $AppUI->redirect("m=system&a=access_denied");
}

require_once($AppUI->getModuleClass("dPplanningOp", "planning"));
require_once($AppUI->getModuleClass("dPbloc"      , "salle"   ));

$urgences      = new COperation;
$where         = array();
$where["date"] = "= '".mbDate()."'";
$order         = "salle_id, chir_id";
$urgences      = $urgences->loadList($where, $order);
foreach($urgences as $keyOp => $op) {
  $urgences[$keyOp]->loadRefsFwd();
  $urgences[$keyOp]->_ref_sejour->loadRefPatient();
}

$listSalles = new CSalle;
$order = "nom";
$listSalles = $listSalles->loadList(null, $order);

// Création du template
require_once( $AppUI->getSystemClass ("smartydp" ) );
$smarty = new CSmartyDP(1);

$smarty->debugging = false;

$smarty->assign("urgences"  , $urgences);
$smarty->assign("listSalles", $listSalles);

$smarty->display("vw_urgences.tpl");

?>