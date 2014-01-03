<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SalleOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$date     = CValue::getOrSession("date", CMbDT::date());
$in_salle = CValue::get("in_salle", 1);
$modif_operation = CCAnDo::edit() || $date >= CMbDT::date();

// Chargement de l'operation selectionnee
$operation_id = CValue::getOrSession("operation_id");
$selOp = new COperation();
$selOp->load($operation_id);
$plageOp = $selOp->loadRefPlageOp();

$listPers = $selOp->loadPersonnelDisponible();

// Creation du tableau d'affectation de personnel
$tabPersonnel = array();

$plageOp->loadAffectationsPersonnel();
$affectations_personnel = $plageOp->_ref_affectations_personnel;
$affectations_plage = array_merge(
  $affectations_personnel["iade"],
  $affectations_personnel["op"],
  $affectations_personnel["op_panseuse"],
  $affectations_personnel["sagefemme"],
  $affectations_personnel["manipulateur"]
);

// Tableau de stockage des affectations
$tabPersonnel["plage"] = array();
$tabPersonnel["operation"] = array();

foreach ($affectations_plage as $key => $affectation_personnel) {
  $affectation = new CAffectationPersonnel();
  $affectation->setObject($selOp);
  $affectation->personnel_id = $affectation_personnel->personnel_id;
  $affectation->parent_affectation_id = $affectation_personnel->_id;
  $affectation->loadMatchingObject();
  if (!$affectation->_id) {
    $affectation->parent_affectation_id = $affectation_personnel->_id;
  }
  $affectation->loadRefPersonnel();
  $affectation->_ref_personnel->loadRefUser();
  $affectation->_ref_personnel->_ref_user->loadRefFunction();
  $tabPersonnel["plage"][$affectation->personnel_id] = $affectation;
}

// Chargement du de l'operation
$affectations_personnel = $selOp->_ref_affectations_personnel;

$affectations_operation = array_merge(
  $affectations_personnel["iade"],
  $affectations_personnel["op"],
  $affectations_personnel["op_panseuse"],
  $affectations_personnel["sagefemme"],
  $affectations_personnel["manipulateur"]
);

foreach ($affectations_operation as $key => $affectation_personnel) {
  $personnel = $affectation_personnel->_ref_personnel;
  if ($affectation_personnel->parent_affectation_id) {
    unset($affectations_operation[$key]);
    continue;
  }
  $tabPersonnel["operation"][] = $affectation_personnel;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("selOp"           , $selOp);
$smarty->assign("tabPersonnel"    , $tabPersonnel);
$smarty->assign("listPers"        , $listPers);
$smarty->assign("modif_operation" , $modif_operation);
$smarty->assign("in_salle"        , $in_salle);

$smarty->display("inc_vw_personnel.tpl");
