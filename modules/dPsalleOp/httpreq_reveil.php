<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPsalleOp
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$date    = CValue::getOrSession("date", mbDate());
$bloc_id = CValue::getOrSession("bloc_id");
$type    = CValue::get("type"); // Type d'affichage => encours, ops, reveil, out
$present_only = CValue::getOrSession("present_only", 0);
$present_only_reel = CValue::getOrSession("present_only_reel", 0);
$modif_operation = CCanDo::edit() || $date >= mbDate();

// Selection des plages opératoires de la journée
$plage = new CPlageOp();
$plage->date = $date;
$plages = $plage->loadMatchingList();

// Selection des salles du bloc
$salle = new CSalle();
$whereSalle = array("bloc_id" => " = '$bloc_id'");
$listSalles = $salle->loadListWithPerms(PERM_READ, $whereSalle);

$where = array();
$where["annulee"] = "= '0'";
$ljoin = array();

if (CAppUI::conf("dPplanningOp COperation use_poste")) {
  $ljoin["poste_sspi"] = "poste_sspi.poste_sspi_id = operations.poste_sspi_id";
  $where[] = "(operations.poste_sspi_id IS NOT NULL AND poste_sspi.bloc_id = '$bloc_id')
              OR (operations.poste_sspi_id IS NULL AND operations.salle_id ". CSQLDataSource::prepareIn(array_keys($listSalles)) . ")";
}
else {
  $where["operations.salle_id"] = CSQLDataSource::prepareIn(array_keys($listSalles));
}
$where[] = "plageop_id ".CSQLDataSource::prepareIn(array_keys($plages))." OR (plageop_id IS NULL AND date = '$date')";

switch ($type) {
  case 'preop':
    $where["entree_salle"] = "IS NULL";
    $order = "time_operation";
    break;
  case 'encours':
    $where["entree_salle"] = "IS NOT NULL";
    $where["sortie_salle"] = "IS NULL";
    $order = "entree_salle";
    break;
  case 'ops':
    $where["sortie_salle"] = "IS NOT NULL";
    $where["entree_reveil"] = "IS NULL";
    $where["sortie_reveil_possible"] = "IS NULL";
    $order = "sortie_salle";
    break;
  case 'reveil':
    $where["entree_reveil"] = "IS NOT NULL";
    $where["sortie_reveil_possible"] = "IS NULL";
    $order = "entree_reveil";
    break;
  case 'out':
    $where["sortie_reveil_possible"] = "IS NOT NULL";
    $order = "sortie_reveil_possible DESC";
    break;
}

$use_poste = CAppUI::conf("dPplanningOp COperation use_poste");

// Chargement des interventions    
$operation = new COperation();
$listOperations = $operation->loadList($where, $order, null, null, $ljoin);

$chirs = CMbObject::massLoadFwdRef($listOperations, "chir_id");
CMbObject::massLoadFwdRef($chirs, "function_id");
CMbObject::massLoadFwdRef($listOperations, "plageop_id");
$sejours = CMbObject::massLoadFwdRef($listOperations, "sejour_id");
CMbObject::massLoadFwdRef($sejours, "patient_id");
if ($use_poste) {
  CMbObject::massLoadFwdRef($listOperations, "poste_sspi_id");
}

$nb_sorties_non_realisees = 0;
$now = mbTime();

$use_sortie_reveil_reel = CAppUI::conf("dPsalleOp COperation use_sortie_reveil_reel");

foreach ($listOperations as $key => $op) {
  $sejour = $op->loadRefSejour(1);
  $sejour->loadNDA();
  
  if ($sejour->type == "exte") {
    unset($listOperations[$key]);
    continue;
  }

  if ($type == "out") {
    if ($present_only && $op->sortie_reveil_possible < $now) {
      unset($listOperations[$key]);
      continue;
    }
    elseif ($present_only_reel && $op->sortie_reveil_reel && $op->sortie_reveil_reel < $now) {
      unset($listOperations[$key]);
      continue;
    }
  }

  $op->loadRefChir(1);
  $op->_ref_chir->loadRefFunction();
  $op->loadRefPlageOp(1);
  $op->loadRefPatient(1);
  $op->loadAffectationsPersonnel();
  
  if ($use_poste) {
    $op->loadRefPoste();
  }
  
  if (($type == "ops" || $type == "reveil") && CModule::getActive("bloodSalvage")) {
    $op->blood_salvage = new CBloodSalvage();
    $where = array();
    $where["operation_id"] = "= '$key'";
    $op->blood_salvage->loadObject($where);
    $op->blood_salvage->loadRefPlageOp();
    $op->blood_salvage->totaltime = "00:00:00";
    if ($op->blood_salvage->recuperation_start && $op->blood_salvage->transfusion_end) {
      $op->blood_salvage->totaltime = mbTimeRelative($op->blood_salvage->recuperation_start, $op->blood_salvage->transfusion_end);
    }
    elseif ($op->blood_salvage->recuperation_start) {
      $from = $op->blood_salvage->recuperation_start;
      $to   = mbDate($op->blood_salvage->_datetime)." ".mbTime();
      $op->blood_salvage->totaltime = mbTimeRelative($from, $to);
    }
  }
  
  if ($type == "reveil" || $type == "out") {
    if ($use_sortie_reveil_reel) {
      if ($op->sortie_reveil_reel > $now) {
        $nb_sorties_non_realisees++;
      }
    }
    elseif ($op->sortie_reveil_possible > $now) {
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

$smarty->assign("personnels"             , $personnels);
$smarty->assign("listOperations"         , $listOperations);
$smarty->assign("plages"                 , $plages);
$smarty->assign("date"                   , $date);
$smarty->assign("isbloodSalvageInstalled", CModule::getActive("bloodSalvage"));
$smarty->assign("hour"                   , mbTime());
$smarty->assign("modif_operation"        , $modif_operation);
$smarty->assign("isImedsInstalled", (CModule::getActive("dPImeds") && CImeds::getTagCIDC(CGroups::loadCurrent())));
$smarty->assign("nb_sorties_non_realisees", $nb_sorties_non_realisees);
$smarty->assign("present_only"           , $present_only);
$smarty->assign("present_only_reel"      , $present_only_reel);

$smarty->display("inc_reveil_$type.tpl");
