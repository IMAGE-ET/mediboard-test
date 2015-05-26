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

// orders & filters
$order_col = CValue::get("order_col");
if ($order_col && $type) {
  CValue::setSession("order_col_" . $type, $order_col);
}

//order way
$order_way = CValue::get("order_way");
if ($order_way && $type) {
  CValue::setSession("order_way_" . $type, $order_way);
}

$order_way_final = CValue::getOrSession("order_way_".$type, CValue::get("order_way"));

switch ($type) {
  case 'preop':
    $where["operations.entree_salle"] = "IS NULL";
    // $order = "operations.time_operation";
    $order_col = CValue::getOrSession("order_col_$type", "time_operation");
    break;

  case 'encours':
    $where["operations.entree_salle"] = "IS NOT NULL";
    $where["operations.sortie_salle"] = "IS NULL";
    // $order = "operations.entree_salle";
    $order_col = CValue::getOrSession("order_col_$type", "entree_salle");
    break;

  case 'ops':
    $where["operations.sortie_salle"] = "IS NOT NULL";
    $where["operations.entree_reveil"] = "IS NULL";
    $where["operations.sortie_reveil_possible"] = "IS NULL";
    // $order = "operations.sortie_salle";
    $order_col = CValue::getOrSession("order_col_$type", "sortie_salle");
    break;

  case 'reveil':
    $where["operations.entree_reveil"] = "IS NOT NULL";
    $where["operations.sortie_reveil_reel"] = "IS NULL";
    // $order = "operations.entree_reveil";
    $order_col = CValue::getOrSession("order_col_$type", "entree_reveil");
    break;

  default:
    $where["operations.sortie_reveil_reel"] = "IS NOT NULL";
    // $order = "operations.sortie_reveil_possible DESC";
    $order_col = CValue::getOrSession("order_col_$type", "sortie_reveil_possible");
    $order_way = CValue::getOrSession("order_way_$type", "DESC");
    break;
}

if ($order_col == "_patient") {
  $order_col = "entree_salle";
}

// Chargement des interventions    
$operation = new COperation();
$listOperations = $operation->loadList($where, "$order_col $order_way_final", null, null, $ljoin);

// Optimisations de chargement
$chirs = CMbObject::massLoadFwdRef($listOperations, "chir_id");
CMbObject::massLoadFwdRef($chirs, "function_id");
CMbObject::massLoadFwdRef($listOperations, "plageop_id");
if ($use_poste) {
  CMbObject::massLoadFwdRef($listOperations, "poste_sspi_id");
  CMbObject::massLoadFwdRef($listOperations, "poste_preop_id");
}

$anesths = CMbObject::massLoadFwdRef($listOperations, "sortie_locker_id");
CMbObject::massLoadFwdRef($anesths, "function_id");

if (in_array($type, array("ops", "reveil")) && CModule::getActive("bloodSalvage")) {
  CMbObject::massCountBackRefs($listOperations, "blood_salvages");
}
$sejours = CMbObject::massLoadFwdRef($listOperations, "sejour_id");
CMbObject::massLoadFwdRef($sejours, "patient_id");

$nb_sorties_non_realisees = 0;
$now = CMbDT::time();

$keywords = explode("|", CAppUI::conf("soins Other ignore_allergies", $group));

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
  $dossier_med = $op->_ref_patient->loadRefDossierMedical();

  $dossier_med->loadRefsAllergies();
  $dossier_med->loadRefsAntecedents();
  $dossier_med->countAntecedents(false);
  $dossier_med->countAllergies();

  $op->loadAffectationsPersonnel();
  $op->loadBrancardage();
  
  if ($use_poste) {
    $op->loadRefPoste();
    $op->loadRefPostePreop();
  }
  $op->loadRefSortieLocker()->loadRefFunction();

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

// Vérification de la check list journalière
$daily_check_lists = array();
$daily_check_list_types = array();
$require_check_list = 0;
$require_check_list_close = 0;
$listChirs   = array();
$listAnesths = array();
$date_close_checklist = null;
$date_open_checklist  = null;

if ($type == "reveil" || $type == "preop") {
  $bloc = new CBlocOperatoire();
  if (!$bloc->load($bloc_id) && count($listSalles)) {
    $salle = reset($listSalles);
    $bloc = $salle->loadRefBloc();
  }

  $require_check_list = CAppUI::conf("dPsalleOp CDailyCheckList active_salle_reveil") && $date >= CMbDT::date();
  $require_check_list_close = $require_check_list;
  $type_checklist = $type == "reveil" ? "ouverture_sspi" : "ouverture_preop";
  $type_close = $type == "reveil" ? "fermeture_sspi" : "fermeture_preop";
  if ($require_check_list) {
    list($check_list_not_validated, $daily_check_list_types, $daily_check_lists) = CDailyCheckList::getCheckLists($bloc, $date, $type_checklist);
    if ($check_list_not_validated == 0) {
      $require_check_list = false;
    }
  }
  if ($require_check_list_close) {
    list($check_list_not_validated_close, $daily_check_list_types_close, $daily_check_lists_close) = CDailyCheckList::getCheckLists($bloc, $date, $type_close);
    if ($check_list_not_validated_close == 0) {
      $require_check_list_close = false;
    }
  }
  $date_close_checklist = CDailyCheckList::getDateLastChecklist($bloc, $type_close);
  $date_open_checklist  = CDailyCheckList::getDateLastChecklist($bloc, $type_checklist);

  if ($require_check_list) {
    // Chargement de la liste du personnel pour le reveil
    if (CModule::getActive("dPpersonnel")) {
      $type_personnel = array("reveil");
      if (count($daily_check_list_types)) {
        $type_personnel = array();
        foreach ($daily_check_list_types as $check_list_type) {
          $validateurs = explode("|", $check_list_type->type_validateur);
          foreach ($validateurs as $validateur) {
            $type_personnel[] = $validateur;
          }
        }
      }
      $personnel  = new CPersonnel();
      $personnels = $personnel->loadListPers(array_unique(array_values($type_personnel)));
    }
    $curr_user = CMediusers::get();
    // Chargement des praticiens
    $listChirs = $curr_user->loadPraticiens(PERM_DENY);
    // Chargement des anesths
    $listAnesths = $curr_user->loadAnesthesistes(PERM_DENY);
  }
}

//tri par patient
if (CValue::getOrSession("order_col_" . $type) == "_patient") {
  $sorter = CMbArray::pluck($listOperations, "_ref_patient", "nom");
  array_multisort($sorter, $order_way_final == "ASC" ? SORT_ASC : SORT_DESC, $listOperations);
  $order_col = CValue::getOrSession("order_col_" . $type);
}

// Création du template
$smarty = new CSmartyDP();

// Daily check lists
$smarty->assign("date_close_checklist"    , $date_close_checklist);
$smarty->assign("date_open_checklist"     , $date_open_checklist);
$smarty->assign("require_check_list"      , $require_check_list);
$smarty->assign("require_check_list_close", $require_check_list_close);
$smarty->assign("daily_check_lists"       , $daily_check_lists);
$smarty->assign("daily_check_list_types"  , $daily_check_list_types);
$smarty->assign("listChirs"               , $listChirs);
$smarty->assign("listAnesths"             , $listAnesths);
$smarty->assign("type"                    , $type);
$smarty->assign("bloc_id"                 , $bloc_id);

$smarty->assign("personnels"              , $personnels);
$smarty->assign("order_way"               , $order_way);
$smarty->assign("order_col"               , $order_col);
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
