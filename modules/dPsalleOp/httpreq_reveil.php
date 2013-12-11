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

CCanDo::checkRead();

$date              = CValue::getOrSession("date", CMbDT::date());
$bloc_id           = CValue::getOrSession("bloc_id");
$type              = CValue::get("type"); // Type d'affichage => encours, ops, reveil, out
$present_only      = CValue::getOrSession("present_only", 0);
$present_only_reel = CValue::getOrSession("present_only_reel", 0);
$modif_operation   = CCanDo::edit() || $date >= CMbDT::date();

// Chargement des Chirurgiens
$chir      = new CMediusers();
$listChirs = $chir->loadPraticiens(PERM_READ);

// Selection des salles du bloc
$salle = new CSalle();
$whereSalle = array("bloc_id" => " = '$bloc_id'");
$listSalles = $salle->loadListWithPerms(PERM_READ, $whereSalle);

// Selection des plages opératoires de la journée
$plage = new CPlageOp();
$where = array();
$where["date"] = "= '$date'";
$where["salle_id"] = CSQLDataSource::prepareIn(array_keys($listSalles));
$plages = $plage->loadList($where);

$where = array();
$where["annulee"] = "= '0'";
$where["operations.chir_id"] = CSQLDataSource::prepareIn(array_keys($listChirs));
$ljoin = array();

if (CAppUI::conf("dPplanningOp COperation use_poste")) {
  $ljoin["poste_sspi"] = "poste_sspi.poste_sspi_id = operations.poste_sspi_id";
  $where[] = "(operations.poste_sspi_id IS NOT NULL AND poste_sspi.bloc_id = '$bloc_id')
              OR (operations.poste_sspi_id IS NULL AND operations.salle_id ". CSQLDataSource::prepareIn(array_keys($listSalles)) . ")";
}
else {
  $where["operations.salle_id"] = CSQLDataSource::prepareIn(array_keys($listSalles));
}
$where[] = "operations.plageop_id ".CSQLDataSource::prepareIn(array_keys($plages))." OR (operations.plageop_id IS NULL AND operations.date = '$date')";

switch ($type) {
  case 'preop':
    $where["operations.entree_salle"] = "IS NULL";
    $order = "operations.time_operation";
    break;
  case 'encours':
    $where["operations.entree_salle"] = "IS NOT NULL";
    $where["operations.sortie_salle"] = "IS NULL";
    $order = "operations.entree_salle";
    break;
  case 'ops':
    $where["operations.sortie_salle"] = "IS NOT NULL";
    $where["operations.entree_reveil"] = "IS NULL";
    $where["operations.sortie_reveil_possible"] = "IS NULL";
    $order = "operations.sortie_salle";
    break;
  case 'reveil':
    $where["operations.entree_reveil"] = "IS NOT NULL";
    $where["operations.sortie_reveil_possible"] = "IS NULL";
    $order = "operations.entree_reveil";
    break;
  case 'out':
    $where["operations.sortie_reveil_possible"] = "IS NOT NULL";
    $order = "operations.sortie_reveil_possible DESC";
    break;
}

$use_poste = CAppUI::conf("dPplanningOp COperation use_poste");

// Chargement des interventions    
$operation = new COperation();
$listOperations = $operation->loadList($where, $order, null, null, $ljoin);

// Optimisations de chargement
$chirs = CMbObject::massLoadFwdRef($listOperations, "chir_id");
CMbObject::massLoadFwdRef($chirs, "function_id");
CMbObject::massLoadFwdRef($listOperations, "plageop_id");
if ($use_poste) {
  CMbObject::massLoadFwdRef($listOperations, "poste_sspi_id");
}
if (($type == "ops" || $type == "reveil") && CModule::getActive("bloodSalvage")) {
  CMbObject::massCountBackRefs($listOperations, "blood_salvages");
}
$sejours = CMbObject::massLoadFwdRef($listOperations, "sejour_id");
CMbObject::massLoadFwdRef($sejours, "patient_id");

$group = CGroups::loadCurrent();
$nb_sorties_non_realisees = 0;
$now = CMbDT::time();

$use_sortie_reveil_reel = CAppUI::conf("dPsalleOp COperation use_sortie_reveil_reel", $group->_guid);

/** @var $op COperation */
foreach ($listOperations as $op) {
  $sejour = $op->loadRefSejour();
  $sejour->loadNDA();
  
  if ($sejour->type == "exte") {
    unset($listOperations[$op->_id]);
    continue;
  }

  if ($type == "out") {
    if ($present_only && $op->sortie_reveil_possible < $now) {
      unset($listOperations[$op->_id]);
      continue;
    }
    elseif ($present_only_reel && $op->sortie_reveil_reel && $op->sortie_reveil_reel < $now) {
      unset($listOperations[$op->_id]);
      continue;
    }
  }

  $op->loadRefChir()->loadRefFunction();
  $op->loadRefPlageOp();
  $op->loadRefPatient();
  $op->loadAffectationsPersonnel();
  $op->loadBrancardage();
  
  if ($use_poste) {
    $op->loadRefPoste();
  }
  
  if (($type == "ops" || $type == "reveil") && CModule::getActive("bloodSalvage")) {
    $salvage = $op->loadRefBloodSalvage();;
    $salvage->loadRefPlageOp();
    $salvage->_totaltime = "00:00:00";
    if ($salvage->recuperation_start && $salvage->transfusion_end) {
      $salvage->_totaltime = CMbDT::timeRelative($salvage->recuperation_start, $salvage->transfusion_end);
    }
    elseif ($salvage->recuperation_start) {
      $from = $salvage->recuperation_start;
      $to   = CMbDT::date($salvage->_datetime)." ".CMbDT::time();
      $salvage->_totaltime = CMbDT::timeRelative($from, $to);
    }
  }
  
  if ($type == "reveil" || $type == "out") {
    if (!$op->sortie_reveil_reel) {
      $nb_sorties_non_realisees++;
    }

    $sejour->loadRefsAffectations();
    if ($sejour->_ref_first_affectation->_id) {
      $sejour->_ref_first_affectation->loadRefLit();
      $sejour->_ref_first_affectation->_ref_lit->loadCompleteView();
    }
  }
}

// Chargement de la liste du personnel pour le reveil
$personnels = array();
if (Cmodule::getActive("dPpersonnel")) {
  $personnel  = new CPersonnel();
  $personnels = $personnel->loadListPers("reveil");
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("personnels"              , $personnels);
$smarty->assign("listOperations"          , $listOperations);
$smarty->assign("plages"                  , $plages);
$smarty->assign("date"                    , $date);
$smarty->assign("isbloodSalvageInstalled" , CModule::getActive("bloodSalvage"));
$smarty->assign("hour"                    , CMbDT::time());
$smarty->assign("modif_operation"         , $modif_operation);
$smarty->assign("isImedsInstalled"        , (CModule::getActive("dPImeds") && CImeds::getTagCIDC($group)));
$smarty->assign("nb_sorties_non_realisees", $nb_sorties_non_realisees);
$smarty->assign("present_only"            , $present_only);
$smarty->assign("present_only_reel"       , $present_only_reel);

$smarty->display("inc_reveil_$type.tpl");
