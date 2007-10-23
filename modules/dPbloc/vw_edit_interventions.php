<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPbloc
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsRead();

if(!($plageop_id = mbGetValueFromGetOrSession("plageop_id"))) {
  $AppUI->setMsg("Vous devez choisir une plage opératoire", UI_MSG_WARNING);
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

// liste des plages du praticien
$listPlages = new CPlageOp();
$listSalle = array();
$where = array();

$where["date"] = "= '$plage->date'";
$where["chir_id"] = "= '$plage->chir_id'";
$listPlages = $listPlages->loadList($where);
foreach($listPlages as $keyPlages=>$valPlages){
  $listPlages[$keyPlages]->loadRefSalle();
}

// liste des anesthesistes
$mediuser = new CMediusers();
$listAnesth = $mediuser->loadListFromType(array("Anesthésiste"));

$plage->loadPersonnel();
$personnels = array();
if (null !== $plage->_ref_personnel) {
  // Chargement de la liste du personnel de bloc
  $pers = new CPersonnel();
  $wherePers = array();
  $ljoinPers["users"] = "personnel.user_id = users.user_id";
  $wherePers["emplacement"] = " = 'op'";
  $orderPers = "users.user_last_name";
  $personnels = $pers->loadList($wherePers, $orderPers, null, null, $ljoinPers);

  foreach($plage->_ref_personnel as $personnel) {
    $personnel->loadPersonnel();
    $personnel->_ref_personnel->loadRefUser();
		unset($personnels[$personnel->_id]);
  }
}


foreach($personnels as $key => $_personnel){
  $_personnel->loadRefUser();	
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("personnels"        , $personnels);
$smarty->assign("listAnesth"        , $listAnesth);
$smarty->assign("listPlages"        , $listPlages);
$smarty->assign("plage"             , $plage);
$smarty->assign("anesth"            , $anesth);
$smarty->assign("list1"             , $list1);
$smarty->assign("list2"             , $list2);
$smarty->assign("max"               , sizeof($list2));

$smarty->display("vw_edit_interventions.tpl");

?>