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
$operation_id   = CValue::getOrSession("operation_id");
$date           = CValue::getOrSession("date");
$salle_id       = CValue::get("salle_id");
$load_checklist = CValue::get("load_checklist", 0);

$selOp = new COperation();
$selOp->load($operation_id);

$currUser = CMediusers::get();

$listAnesths = $currUser->loadAnesthesistes(PERM_DENY);
$listChirs = $currUser->loadPraticiens(PERM_DENY);
$anesth_id = "";
$type_personnel = array("op", "op_panseuse", "iade", "sagefemme", "manipulateur");
$listValidateurs = $operateurs_disp_vasc = array();

// Vérification de la check list journalière
if ($salle_id) {
  $salle = new CSalle();
  $salle->load($salle_id);

  $daily_check_lists = array();
  $daily_check_list_types = array();
  $require_check_list = CAppUI::conf("dPsalleOp CDailyCheckList active") && $date >= CMbDT::date() && !$currUser->isPraticien();

  if ($require_check_list) {
    list($check_list_not_validated, $daily_check_list_types, $daily_check_lists) = CDailyCheckList::getCheckLists($salle, $date);

    if ($salle->cheklist_man && !$load_checklist) {
      $check_list_not_validated = 0;
    }
    if ($check_list_not_validated == 0) {
      $require_check_list = false;
    }
  }

  // Chargement des check lists de l'OMS
  if ($require_check_list) {
    if (count($daily_check_list_types)) {
      $type_personnel = array();
      foreach ($daily_check_list_types as $check_list_type) {
        $validateurs = explode("|", $check_list_type->type_validateur);
        foreach ($validateurs as $validateur) {
          $type_personnel[] = $validateur;
        }
      }
    }

    $listValidateurs = CPersonnel::loadListPers(array_unique(array_values($type_personnel)), true, true);
    $operateurs_disp_vasc = implode("-", array_merge(CMbArray::pluck($listChirs, "_id"), CMbArray::pluck($listValidateurs, "user_id")));

    $smarty = new CSmartyDP();
    $smarty->assign("listAnesths"           , $listAnesths);
    $smarty->assign("listChirs"             , $listChirs);
    $smarty->assign("listValidateurs"       , $listValidateurs);

    // Daily check lists
    $smarty->assign("require_check_list"    , $require_check_list);
    $smarty->assign("daily_check_lists"     , $daily_check_lists);
    $smarty->assign("daily_check_list_types", $daily_check_list_types);

    $smarty->display("inc_operation.tpl");
    CApp::rip();
  }
}

// Pre-anesth, pre-op, post-op
// Don't load them if we have a daily check list to fill...

$operation_check_lists = $operation_check_item_categories = array();
$check_lists_no_has    = $check_items_no_has_categories   = $listValidateurs_no_has = array();

$listAnesthType = array();

if ($selOp->_id) {
  $selOp->canDo();
  $selOp->loadRefs();

  $selOp->loadRefsFiles();
  $selOp->loadRefsDocs();

  $consult_anesth = $selOp->loadRefsConsultAnesth();
  $consult_anesth->countDocItems();

  $consultation = $consult_anesth->loadRefConsultation();
  $consultation->countDocItems();
  $consultation->canRead();
  $consultation->canEdit();

  $selOp->loadRefPlageOp(true);

  $selOp->loadRefChir()->loadRefFunction();
  $selOp->loadRefPatient();
  $selOp->loadRefCommande();

  $date = CMbDT::date($selOp->_datetime);

  // Récupération de l'utilisateur courant
  $currUser = CMediusers::get();
  $currUser->isAnesth();
  $currUser->isPraticien();

  $selOp->countExchanges();
  $selOp->loadBrancardage();
  $selOp->isCoded();
  $selOp->_ref_consult_anesth->loadRefsTechniques();

  $sejour = $selOp->_ref_sejour;

  $sejour->loadRefDossierMedical();
  $sejour->_ref_dossier_medical->loadRefsBack();
  $sejour->loadRefsConsultAnesth();
  $sejour->_ref_consult_anesth->loadRefsFwd();
  $sejour->loadRefCurrAffectation();

  if (CModule::getActive("maternite")) {
    $grossesse = $sejour->loadRefGrossesse();
    $grossesse->_ref_last_operation = $selOp;
  }

  $patient = $sejour->_ref_patient;
  $patient->loadRefPhotoIdentite();
  $dossier_medical = $patient->loadRefDossierMedical();
  $dossier_medical->loadRefsAllergies();
  $dossier_medical->countAllergies();

  $selOp->_ref_plageop->loadRefsFwd();
  $selOp->_ref_consult_anesth->_ref_consultation->loadRefsBack();
  $selOp->_ref_consult_anesth->_ref_consultation->loadRefPraticien()->loadRefFunction();

  if (!$selOp->prat_visite_anesth_id && $selOp->_ref_anesth->_id) {
    $selOp->prat_visite_anesth_id = $selOp->_ref_anesth->_id;
  }
  $selOp->loadLiaisonLibelle();
  $listAnesthType = new CTypeAnesth();
  $listAnesthType = $listAnesthType->loadGroupList();

  // Vérification de la check list journalière
  $daily_check_list = CDailyCheckList::getList($selOp->_ref_salle, $date);
  $daily_check_list->loadItemTypes();
  $daily_check_list->loadBackRefs('items');

  $cat = new CDailyCheckItemCategory();
  $cat->target_class = "CSalle";
  $daily_check_item_categories = $cat->loadMatchingList();

  // Chargement des 3 check lists de l'OMS
  $operation_check_list = new CDailyCheckList();
  $cat = new CDailyCheckItemCategory();
  $where_cat = array();
  $where_cat["target_class"] = " = 'COperation'";
  $where_cat["list_type_id"] = "IS NULL";
  $lists = array();

  // Pre-anesth, pre-op, post-op
  foreach ($operation_check_list->_specs["type"]->_list as $type) {
    $list = CDailyCheckList::getList($selOp, null, $type);
    $list->loadItemTypes();
    $list->loadRefsFwd();
    $list->loadBackRefs('items');
    $list->isReadonly();
    $list->_ref_object->loadRefPraticien();
    $operation_check_lists[$type] = $list;

    $where_cat["type"] = " = '$type'";
    $operation_check_item_categories[$type] = $cat->loadList($where_cat, "title");
  }
  $type_personnel_no_has = array();
  foreach (CDailyCheckListGroup::loadChecklistGroup() as $_checklist_group) {
    foreach ($_checklist_group->_ref_check_liste_types as $_checklist_type) {
      $list = CDailyCheckList::getList($selOp, null, null, $_checklist_type->_id);
      $list->loadItemTypes();
      $list->loadRefsFwd();
      $list->loadBackRefs('items');
      $list->isReadonly();
      $list->_ref_object->loadRefPraticien();
      $check_lists_no_has[$_checklist_type->_id] = $list;

      $where_cat = array();
      $where_cat["target_class"] = " = 'COperation'";
      $where_cat["list_type_id"] = " = '$_checklist_type->_id'";
      $check_items_no_has_categories[$_checklist_type->_id] = $cat->loadList($where_cat, "title");
    }
    $validateurs = explode("|", $list->loadRefListType()->type_validateur);
    foreach ($validateurs as $validateur) {
      $type_personnel_no_has[] = $validateur;
    }
  }
  $listValidateurs_no_has = CPersonnel::loadListPers(array_unique(array_values($type_personnel_no_has)), true, true);

  $anesth_id = ($selOp->anesth_id) ? $selOp->anesth_id : $selOp->_ref_plageop->anesth_id;
  $listValidateurs = CPersonnel::loadListPers($type_personnel, true, true);
  $operateurs_disp_vasc = implode("-", array_merge(CMbArray::pluck($listChirs, "_id"), CMbArray::pluck($listValidateurs, "user_id")));
}

$group = CGroups::loadCurrent();
$group->loadConfigValues();

$anesth = new CMediusers();
$anesth->load($anesth_id);

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("selOp"                  , $selOp);
$smarty->assign("date"                   , $date);
$smarty->assign("currUser"               , $currUser);
$smarty->assign("listAnesthType"         , $listAnesthType);
$smarty->assign("listAnesths"            , $listAnesths);
$smarty->assign("list_anesths"           , $listAnesths);
$smarty->assign("operateurs_disp_vasc"   , $operateurs_disp_vasc);
$smarty->assign("modeDAS"                , CAppUI::conf("dPsalleOp CDossierMedical DAS"));
$smarty->assign("modif_operation"        , $selOp->canEdit() || $date >= CMbDT::date());
$smarty->assign("isImedsInstalled"       , (CModule::getActive("dPImeds") && CImeds::getTagCIDC($group)));
$smarty->assign("codage_prat"            , $group->_configs["codage_prat"]);
$smarty->assign("_is_dentiste"           , $selOp->_ref_chir->isDentiste());
$smarty->assign("listValidateurs"        , $listValidateurs);
$smarty->assign("anesth_id"              , $anesth_id);
$smarty->assign("anesth"                 , $anesth);
$smarty->assign("create_dossier_anesth"  , 1);
$smarty->assign("require_check_list"     , 0);
// Operation check lists
$smarty->assign("operation_check_lists"           , $operation_check_lists);
$smarty->assign("operation_check_item_categories" , $operation_check_item_categories);
$smarty->assign("check_lists_no_has"              , $check_lists_no_has);
$smarty->assign("check_items_no_has_categories"   , $check_items_no_has_categories);
$smarty->assign("list_chirs"                      , $listChirs);
$smarty->assign("listValidateurs_no_has"          , $listValidateurs_no_has);

$smarty->display("inc_operation.tpl");