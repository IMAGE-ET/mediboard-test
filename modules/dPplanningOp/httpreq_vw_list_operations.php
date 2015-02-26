<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage PlanningOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$userSel   = CMediusers::get(CValue::getOrSession("pratSel"));

$date      = CValue::getOrSession("date", CMbDT::date());
$canceled  = CValue::getOrSession("canceled", 0);
$board     = CValue::get("board", 0);
$boardItem = CValue::get("boardItem", 0);

$nb_canceled = 0;
$current_group = CGroups::loadCurrent();

// Urgences du jour
$list_urgences = array();
$operation = new COperation();

if ($userSel->_id) {
  $where = array();

  $where["date"] = "= '$date'";
  $where["plageop_id"] = "IS NULL";
  $where[] = "chir_id = '$userSel->_id' OR anesth_id = '$userSel->_id'";
  if (!$canceled) {
    $where["annulee"] = "= '0'";
  }
  /** @var COperation[] $list_urgences */
  $list_urgences = $operation->loadList($where, "annulee, date");

  $where["annulee"] = "= '1'";
  $nb_canceled += $operation->countList($where);

  $sejours = CMbObject::massLoadFwdRef($list_urgences, "sejour_id");
  CMbObject::massLoadFwdRef($sejours, "patient_id");

  foreach ($list_urgences as $_urg) {
    $_urg->canDo();
    $_urg->loadRefsFwd();
    $_urg->loadRefCommande();
    $_sejour = $_urg->_ref_sejour;
    $_urg->loadRefsDocs();
    foreach ($_urg->_ref_documents as $_document) {
      $_document->canDo();
    }
    
    $_sejour->loadRefsFwd();
    $_sejour->canDo();
    $_sejour->_ref_patient->loadRefDossierMedical()->countAllergies();
    $_sejour->loadRefsDocs();

    $presc = $_sejour->loadRefPrescriptionSejour();
    if ($presc && $presc->_id) {
      $presc->countLinesMedsElements($userSel->_id);
    }
    foreach ($_sejour->_ref_documents as $_document) {
      $_document->canDo();
    }
  }
}

// Liste des opérations du jour sélectionné
$list_plages = array();

if ($userSel->_id) {
  $userSel->loadBackRefs("secondary_functions");
  $secondary_specs = array();
  foreach ($userSel->_back["secondary_functions"] as  $_sec_spec) {
    /** @var CSecondaryFunction $_sec_spec */
    $_sec_spec->loadRefFunction();
    $_sec_spec->loadRefUser();
    $_function = $_sec_spec->_ref_function;
    $secondary_specs[$_function->_id] = $_function;
  }
  $where = array();
  $where["date"] = "= '$date'";
  
  $in = "";
  if (count($secondary_specs)) {
    $in = " OR plagesop.spec_id ".CSQLDataSource::prepareIn(array_keys($secondary_specs));
  }
  
  $where[] = "plagesop.chir_id = '$userSel->_id'
              OR plagesop.anesth_id = '$userSel->_id'
              OR plagesop.spec_id = '$userSel->function_id' $in";
  $order = "debut, salle_id";
  
  $plageop = new CPlageOp();

  /** @var CPlageOp[] $list_plages */
  $list_plages = $plageop->loadList($where, $order);

  // Chargement d'optimisation

  CMbObject::massLoadFwdRef($list_plages, "chir_id");
  CMbObject::massLoadFwdRef($list_plages, "anesth_id");
  CMbObject::massLoadFwdRef($list_plages, "spec_id");
  CMbObject::massLoadFwdRef($list_plages, "salle_id");

  CMbObject::massCountBackRefs($list_plages, "notes");

  foreach ($list_plages as $_plage) {
    $op_canceled = new COperation();
    $op_canceled->annulee = 1;
    $op_canceled->plageop_id = $_plage->_id;
    $nb_canceled += $op_canceled->countMatchingList();

    $_plage->loadRefChir();
    $_plage->loadRefAnesth();
    $_plage->loadRefSpec();
    $_plage->loadRefSalle();
    $_plage->makeView();
    $_plage->loadRefsNotes();

    //compare current group with bloc group
    $_plage->_ref_salle->loadRefBloc();
    if ($_plage->_ref_salle->_ref_bloc->group_id != $current_group->_id) {
      $_plage->_ref_salle->_ref_bloc->loadRefGroup();
    }

    $where = array();
    if ($userSel->_id && !$userSel->isAnesth()) {
      $where["chir_id"] = "= '$userSel->_id'";
    }
    
    $_plage->loadRefsOperations($canceled, "annulee ASC, rank, rank_voulu, horaire_voulu", true, null, $where);

    // Chargement d'optimisation

    CMbObject::massLoadFwdRef($_plage->_ref_operations, "chir_id");
    $sejours = CMbObject::massLoadFwdRef($_plage->_ref_operations, "sejour_id");
    CMbObject::massLoadFwdRef($sejours, "patient_id");
    
    foreach ($_plage->_ref_operations as $_op) {
      $_op->loadRefsFwd();
      $_sejour = $_op->_ref_sejour;
      $_op->loadRefsDocs();
      foreach ($_op->_ref_documents as $_doc) {
        $_doc->canDo();
      }
      $_op->canDo();
      $_op->loadRefCommande();
      $_sejour->canDo();
      $_sejour->loadRefsFwd();
      $_sejour->_ref_patient->loadRefDossierMedical()->countAllergies();
      $_sejour->loadRefsDocs();

      $presc = $_sejour->loadRefPrescriptionSejour();
      if ($presc && $presc->_id) {
        $presc->countLinesMedsElements($userSel->_id);
      }
      foreach ($_sejour->_ref_documents as $_doc) {
        $_doc->canDo();
      }
    }
  }
}

// Praticien concerné
$user = CMediusers::get();
if ($user->isPraticien()) {
  $praticien = $user;
}
else {
  $praticien = new CMediusers();
  $praticien->load(CValue::getOrSession("pratSel", CValue::getOrSession("praticien_id")));
}

$praticien->loadRefFunction();
$praticien->_ref_function->loadRefGroup();
$praticien->canDo();

// Compter les modèles d'étiquettes
$modele_etiquette = new CModeleEtiquette;

$where = array();
$where['object_class'] = "= 'COperation'";
$where["group_id"] = " = '".CGroups::loadCurrent()->_id."'";

$nb_modeles_etiquettes_operation = $modele_etiquette->countList($where);

$where['object_class'] = "= 'CSejour'";
$nb_modeles_etiquettes_sejour = $modele_etiquette->countList($where);

$nb_printers = 0;

if (CModule::getActive("printing")) {
  // Chargement des imprimantes pour l'impression d'étiquettes 
  $user_printers = CMediusers::get();
  $function      = $user_printers->loadRefFunction();
  $nb_printers   = $function->countBackRefs("printers");
}

$compte_rendu = new CCompteRendu();

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("praticien"     , $praticien);
$smarty->assign("boardItem"     , $boardItem);
$smarty->assign("date"          , $date);
$smarty->assign("canceled"      , $canceled);
$smarty->assign("listUrgences"  , $list_urgences);
$smarty->assign("listDay"       , $list_plages);
$smarty->assign("nb_canceled"   , $nb_canceled);
$smarty->assign("board"         , $board);
$smarty->assign("nb_printers"   , $nb_printers);
$smarty->assign("can_doc"       , $compte_rendu->loadPermClass());
$smarty->assign("nb_modeles_etiquettes_sejour", $nb_modeles_etiquettes_sejour);
$smarty->assign("nb_modeles_etiquettes_operation", $nb_modeles_etiquettes_operation);

$smarty->display("inc_list_operations.tpl");
