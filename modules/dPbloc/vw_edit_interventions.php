<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPbloc
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$getInstalledPpers = CModule::getInstalled("dPpersonnel");

$can->needsRead();

if(!($plageop_id = mbGetValueFromGetOrSession("plageop_id"))) {
  $AppUI->msg = "Vous devez choisir une plage opératoire";
  $AppUI->redirect("m=dPbloc&tab=vw_edit_planning");
}

$anesth = new CTypeAnesth;
$orderanesth = "name";
$anesth = $anesth->loadList(null,$orderanesth);

// Infos sur la plage opératoire
$plage = new CPlageOp;
$plage->load($plageop_id);
$plage->loadRefsFwd();

// Liste de droite
$list1 = new COperation;
$where = array();
$where["operations.plageop_id"] = "= '$plageop_id'";
$where["rank"] = "= '0'";
$ljoin = array();
$ljoin["plagesop"] = "operations.plageop_id = plagesop.plageop_id";
$order = "operations.temp_operation";
$list1 = $list1->loadList($where, $order, null, null, $ljoin);
foreach($list1 as $key => $value) {
  $list1[$key]->loadRefsFwd();
  $list1[$key]->_ref_sejour->loadRefsFwd();
}

// Liste de gauche
$list2 = new COperation;
$where["rank"] = "!= '0'";
$order = "operations.rank";
$list2 = $list2->loadList($where, $order, null, null, $ljoin);
foreach($list2 as $key => $value) {
  $list2[$key]->loadRefsFwd();
  $list2[$key]->_ref_sejour->loadRefsFwd();
}


if($getInstalledPpers){
  // liste des anesthesistes
  $mediuser = new CMediusers();
  $listAnesth = $mediuser->loadListFromType(array("Anesthésiste"));

  // liste du personnel de bloc
  $mediuser = new CMediusers();
  $listPers = $mediuser->loadListFromType(array("Personnel"));

  // liste du personnel présent dans la plage opératoire
  $wherePersonnel["object_id"] = " = '$plage->_id'";
  $wherePersonnel["object_class"] = " = '$plage->_class_name'";

  $affectation = new CAffectationPersonnel();
  $affectation->updateFormFields();

  $affectations = $affectation->loadList($wherePersonnel);

  $personnels = array();

  foreach($affectations as $key=>$value){
	$med = new CMediusers();
	$personnels[$value->_id] = $med->load($value->user_id);
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("getInstalledPpers" , $getInstalledPpers);

if($getInstalledPpers){
  $smarty->assign("personnels"        , $personnels);
  $smarty->assign("affectations"      , $affectations);
  $smarty->assign("listAnesth"        , $listAnesth);
  $smarty->assign("listPers"          , $listPers);
}
$smarty->assign("plage"             , $plage);
$smarty->assign("anesth"            , $anesth);
$smarty->assign("list1"             , $list1);
$smarty->assign("list2"             , $list2);
$smarty->assign("max"               , sizeof($list2));

$smarty->display("vw_edit_interventions.tpl");

?>