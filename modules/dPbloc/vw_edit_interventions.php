<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can, $m;

$can->needsRead();

if(!($plageop_id = CValue::getOrSession("plageop_id"))) {
  CAppUI::setMsg("Vous devez choisir une plage opératoire", UI_MSG_WARNING);
  CAppUI::redirect("m=dPbloc&tab=vw_edit_planning");
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

$where["date"]    = "= '$plage->date'";
$where["chir_id"] = "= '$plage->chir_id'";
$listPlages = $listPlages->loadList($where);
foreach($listPlages as $keyPlages=>$valPlages){
  $listPlages[$keyPlages]->loadRefSalle();
}

// liste des anesthesistes
$mediuser = new CMediusers();
$listAnesth = $mediuser->loadListFromType(array("Anesthésiste"));

// Chargement des affectation du personnel dans la plage
$plage->loadAffectationsPersonnel();

// Chargement du personnel
$listPersIADE = CPersonnel::loadListPers("iade");
$listPersAideOp = CPersonnel::loadListPers("op");
$listPersPanseuse = CPersonnel::loadListPers("op_panseuse");

$affectations_plage["iade"] = $plage->_ref_affectations_personnel["iade"];
$affectations_plage["op"] = $plage->_ref_affectations_personnel["op"];
$affectations_plage["op_panseuse"] = $plage->_ref_affectations_personnel["op_panseuse"];

if (!$affectations_plage["iade"]){
  $affectations_plage["iade"] = array();
}
if (!$affectations_plage["op"]){
  $affectations_plage["op"] = array();
}
if (!$affectations_plage["op_panseuse"]){
  $affectations_plage["op_panseuse"] = array();
}

foreach($affectations_plage["iade"] as $key => $affectation){
  if(array_key_exists($affectation->personnel_id, $listPersIADE)){
    unset($listPersIADE[$affectation->personnel_id]);
  }
}
foreach($affectations_plage["op"] as $key => $affectation){
  if(array_key_exists($affectation->personnel_id, $listPersAideOp)){
    unset($listPersAideOp[$affectation->personnel_id]);
  }
}
foreach($affectations_plage["op_panseuse"] as $key => $affectation){
  if(array_key_exists($affectation->personnel_id, $listPersPanseuse)){
    unset($listPersPanseuse[$affectation->personnel_id]);
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("affectations_plage", $affectations_plage);
$smarty->assign("listPersIADE"      , $listPersIADE      );
$smarty->assign("listPersAideOp"    , $listPersAideOp    );
$smarty->assign("listPersPanseuse"  , $listPersPanseuse  );
$smarty->assign("listAnesth"        , $listAnesth        );
$smarty->assign("listPlages"        , $listPlages        );
$smarty->assign("listHours"         , CPlageOp::$hours   );
$smarty->assign("listMins"          , CPlageOp::$minutes );
$smarty->assign("plage"             , $plage             );
$smarty->assign("anesth"            , $anesth            );
$smarty->assign("list1"             , $list1             );
$smarty->assign("list2"             , $list2             );
$smarty->assign("max"               , sizeof($list2)     );
$smarty->display("vw_edit_interventions.tpl");

?>