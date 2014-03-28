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
$modif_operation   = CCanDo::edit() || $date >= CMbDT::date();

$curr_user = CMediusers::get();
$group = CGroups::loadCurrent();

$use_poste = CAppUI::conf("dPplanningOp COperation use_poste");
$password_sortie = CAppUI::conf("dPsalleOp COperation password_sortie", $group->_guid);

// Chargement des Chirurgiens
$listChirs = $curr_user->loadPraticiens(PERM_READ);

// Selection des salles du bloc
$salle = new CSalle();
$whereSalle = array("bloc_id" => " = '$bloc_id'");
$listSalles = $salle->loadListWithPerms(PERM_READ, $whereSalle);

// Selection des plages opératoires de la journée
$plage = new CPlageOp();
$where = array();
$where["date"] = "= '$date'";
// Filtre sur les salles qui pose problème
//$where["salle_id"] = CSQLDataSource::prepareIn(array_keys($listSalles));
$plages = $plage->loadList($where);

$where = array();
$where["annulee"] = "= '0'";
$where["operations.chir_id"] = CSQLDataSource::prepareIn(array_keys($listChirs));
$ljoin = array();

if ($use_poste) {
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
    $where["operations.sortie_reveil_reel"] = "IS NULL";
    $order = "operations.entree_reveil";
    break;
  case 'out':
    $where["operations.sortie_reveil_reel"] = "IS NOT NULL";
    $order = "operations.sortie_reveil_possible DESC";
    break;
}

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
if ($password_sortie) {
  $anesths = CMbObject::massLoadFwdRef($listOperations, "sortie_locker_id");
  CMbObject::massLoadFwdRef($anesths, "function_id");
}
if (in_array($type, array("ops", "reveil")) && CModule::getActive("bloodSalvage")) {
  CMbObject::massCountBackRefs($listOperations, "blood_salvages");
}
$sejours = CMbObject::massLoadFwdRef($listOperations, "sejour_id");
CMbObject::massLoadFwdRef($sejours, "patient_id");

$nb_sorties_non_realisees = 0;
$now = CMbDT::time();

/** @var $op COperation */
foreach ($listOperations as $op) {
  $sejour = $op->loadRefSejour();

  if ($sejour->type == "exte") {
    unset($listOperations[$op->_id]);
    continue;
  }

  $sejour->loadNDA();

  $op->loadRefChir()->loadRefFunction();
  $op->loadRefPlageOp();
  $op->loadRefPatient();
  $op->loadAffectationsPersonnel();
  $op->loadBrancardage();
  
  if ($use_poste) {
    $op->loadRefPoste();
  }
  if ($password_sortie) {
    $op->loadRefSortieLocker()->loadRefFunction();
  }

  if (in_array($type, array("ops", "reveil")) && CModule::getActive("bloodSalvage")) {
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
  
  if (in_array($type, array("out", "reveil"))) {
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
if (in_array($type, array("ops", "reveil")) && Cmodule::getActive("dPpersonnel")) {
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
$smarty->assign("is_anesth"               , $curr_user->isAnesth());

$smarty->display("inc_reveil_$type.tpl");
