<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage dPpersonnel
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */
 
CCanDo::checkEdit();

if (!($plageop_id = CValue::getOrSession("plageop_id"))) {
  CAppUI::setMsg("Vous devez choisir une plage opératoire", UI_MSG_WARNING);
  CAppUI::redirect("m=dPbloc&tab=vw_edit_planning");
}

// Infos sur la plage opératoire
$plage = new CPlageOp();
$plage->load($plageop_id);
if (!$plage->temps_inter_op) {
  $plage->temps_inter_op = "00:00:00";
}

// liste des anesthesistes
$mediuser = new CMediusers();
$listAnesth = $mediuser->loadListFromType(array("Anesthésiste"));

// Chargement des affectation du personnel dans la plage
$plage->loadAffectationsPersonnel();

// Chargement du personnel
$listPersIADE     = CPersonnel::loadListPers("iade");
$listPersAideOp   = CPersonnel::loadListPers("op");
$listPersPanseuse = CPersonnel::loadListPers("op_panseuse");
$listPersSageFem  = CPersonnel::loadListPers("sagefemme");
$listPersManip    = CPersonnel::loadListPers("manipulateur");

$affectations_plage["iade"]         = $plage->_ref_affectations_personnel["iade"];
$affectations_plage["op"]           = $plage->_ref_affectations_personnel["op"];
$affectations_plage["op_panseuse"]  = $plage->_ref_affectations_personnel["op_panseuse"];
$affectations_plage["sagefemme"]    = $plage->_ref_affectations_personnel["sagefemme"];
$affectations_plage["manipulateur"] = $plage->_ref_affectations_personnel["manipulateur"];

if (!$affectations_plage["iade"]) {
  $affectations_plage["iade"] = array();
}
if (!$affectations_plage["op"]) {
  $affectations_plage["op"] = array();
}
if (!$affectations_plage["op_panseuse"]) {
  $affectations_plage["op_panseuse"] = array();
}
if (!$affectations_plage["sagefemme"]) {
  $affectations_plage["sagefemme"] = array();
}
if (!$affectations_plage["manipulateur"]) {
  $affectations_plage["manipulateur"] = array();
}

foreach ($affectations_plage["iade"] as $key => $affectation) {
  if (array_key_exists($affectation->personnel_id, $listPersIADE)) {
    unset($listPersIADE[$affectation->personnel_id]);
  }
}
foreach ($affectations_plage["op"] as $key => $affectation) {
  if (array_key_exists($affectation->personnel_id, $listPersAideOp)) {
    unset($listPersAideOp[$affectation->personnel_id]);
  }
}
foreach ($affectations_plage["op_panseuse"] as $key => $affectation) {
  if (array_key_exists($affectation->personnel_id, $listPersPanseuse)) {
    unset($listPersPanseuse[$affectation->personnel_id]);
  }
}
foreach ($affectations_plage["sagefemme"] as $key => $affectation) {
  if (array_key_exists($affectation->personnel_id, $listPersSageFem)) {
    unset($listPersSageFem[$affectation->personnel_id]);
  }
}
foreach ($affectations_plage["manipulateur"] as $key => $affectation) {
  if (array_key_exists($affectation->personnel_id, $listPersManip)) {
    unset($listPersManip[$affectation->personnel_id]);
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("affectations_plage", $affectations_plage);
$smarty->assign("listPersIADE"      , $listPersIADE);
$smarty->assign("listPersAideOp"    , $listPersAideOp);
$smarty->assign("listPersPanseuse"  , $listPersPanseuse);
$smarty->assign("listPersSageFem"   , $listPersSageFem);
$smarty->assign("listPersManip"     , $listPersManip);
$smarty->assign("listAnesth"        , $listAnesth);
$smarty->assign("plage"             , $plage);

$smarty->display("inc_view_personnel_plage.tpl");
