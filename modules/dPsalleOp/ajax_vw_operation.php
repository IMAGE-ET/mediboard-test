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
$user = CUser::get();

$op = CValue::getOrSession("op");
$selOp = new COperation();
$selOp->load($op);
if (!$selOp->_id) {
  CAppUI::stepAjax(CAppUI::tr("COperation.none"));
  CApp::rip();
}

$selOp->canDo();
$selOp->loadRefs();

$date = CMbDT::date($selOp->_datetime);
$modif_operation = $selOp->canEdit() || $date >= CMbDT::date();

// Récupération de l'utilisateur courant
$currUser = new CMediusers();
$currUser->load($user->_id);
$currUser->isAnesth();
$currUser->isPraticien();

// Chargement des praticiens
$listAnesths = new CMediusers();
$listAnesths = $listAnesths->loadAnesthesistes(PERM_DENY);

$listChirs = new CMediusers();
$listChirs = $listChirs->loadPraticiens(PERM_DENY);

// Sauvegarde en session du bloc (pour preselectionner dans la salle de reveil)
$salle = new CSalle();
$salle->load($selOp->salle_id);

$protocoles = array();
$anesth_id = "";

$selOp->countExchanges();
$selOp->isCoded();
$selOp->_ref_consult_anesth->loadRefsTechniques();

$sejour = $selOp->_ref_sejour;

$sejour->loadExtDiagnostics();
$sejour->loadRefDossierMedical();
$sejour->_ref_dossier_medical->loadRefsBack();
$sejour->loadRefsConsultAnesth();
$sejour->loadRefsPrescriptions();
$sejour->_ref_consult_anesth->loadRefsFwd();
$sejour->loadRefCurrAffectation();

$patient = $sejour->_ref_patient;
$patient->loadRefPhotoIdentite();
$patient->loadRefDossierMedical();

if (!$selOp->_ref_consult_anesth->_id) {
  $patient->loadRefsConsultations();

  foreach ($patient->_ref_consultations as $_consultation) {
    $_consultation->loadRefsDossiersAnesth();
    $_consultation->loadRefPlageConsult();
    foreach ($_consultation->_refs_dossiers_anesth as $_dossier_anesth) {
      $_dossier_anesth->loadRefOperation();
    }
  }
}

$selOp->getAssociationCodesActes();
$selOp->loadExtCodesCCAM();
$selOp->loadPossibleActes();

$selOp->_ref_plageop->loadRefsFwd();

// Affichage des données
$listChamps = array(
  1 => array("hb", "ht", "ht_final", "plaquettes"),
  2 => array("creatinine", "_clairance", "na", "k"),
  3 => array("tp", "tca", "tsivy", "ecbu"),
);
$cAnesth = $selOp->_ref_consult_anesth;
foreach ($listChamps as $keyCol=>$aColonne) {
  foreach ($aColonne as $keyChamp=>$champ) {
    $verifchamp = true;
    if ($champ == "tca") {
      $champ2 = $cAnesth->tca_temoin;
    }
    else {
      $champ2 = false;
      if (($champ == "ecbu" && $cAnesth->ecbu == "?") || ($champ == "tsivy" && $cAnesth->tsivy == "00:00:00")) {
        $verifchamp = false;
      }
    }
    $champ_exist = $champ2 || ($verifchamp && $cAnesth->$champ);
    if (!$champ_exist) {
      unset($listChamps[$keyCol][$keyChamp]);
    }
  }
}

$selOp->_ref_consult_anesth->_ref_consultation->loadRefsBack();
$selOp->_ref_consult_anesth->_ref_consultation->loadRefPraticien()->loadRefFunction();

// Chargement de la prescription de sejour
if (CModule::getActive("dPprescription")) {
  $prescription = new CPrescription();
  $prescription->object_id = $selOp->sejour_id;
  $prescription->object_class = "CSejour";
  $prescription->type = "sejour";
  $prescription->loadMatchingObject();
}

$anesth_id = ($selOp->anesth_id) ? $selOp->anesth_id : $selOp->_ref_plageop->anesth_id;
if ($anesth_id && CModule::getActive('dPprescription')) {
  $protocoles = CPrescription::getAllProtocolesFor($anesth_id, null, null, 'CSejour', 'sejour');
}

if (!$selOp->prat_visite_anesth_id && $selOp->_ref_anesth->_id) {
  $selOp->prat_visite_anesth_id = $selOp->_ref_anesth->_id;
}

$listAnesthType = new CTypeAnesth();
$orderanesth = "name";
$listAnesthType = $listAnesthType->loadList(null, $orderanesth);

// Tableau d'unités
$unites = array();
$unites["hb"]         = array("nom"=>"Hb","unit"=>"g/dl");
$unites["ht"]         = array("nom"=>"Ht","unit"=>"%");
$unites["ht_final"]   = array("nom"=>"Ht final","unit"=>"%");
$unites["plaquettes"] = array("nom"=>"Plaquettes","unit"=>"");
$unites["creatinine"] = array("nom"=>"Créatinine","unit"=>"mg/l");
$unites["_clairance"] = array("nom"=>"Clairance de Créatinine","unit"=>"ml/min");
$unites["na"]         = array("nom"=>"Na+","unit"=>"mmol/l");
$unites["k"]          = array("nom"=>"K+","unit"=>"mmol/l");
$unites["tp"]         = array("nom"=>"TP","unit"=>"%");
$unites["tca"]        = array("nom"=>"TCA","unit"=>"s");
$unites["tsivy"]      = array("nom"=>"TS Ivy","unit"=>"");
$unites["ecbu"]       = array("nom"=>"ECBU","unit"=>"");

// Initialisation d'un acte NGAP
$acte_ngap = CActeNGAP::createEmptyFor($selOp);

// Si le module Tarmed est installé chargement d'un acte tarmed et caisse
$acte_tarmed = null;
$acte_caisse = null;
if (CModule::getActive("tarmed")) {
  $acte_tarmed = CActeTarmed::createEmptyFor($selOp);
  $acte_caisse = CActeCaisse::createEmptyFor($selOp);
}
$total_tarmed = $selOp->loadRefsActesTarmed();
$total_caisse = $selOp->loadRefsActesCaisse();
$soustotal_base = array("tarmed" => $total_tarmed["base"], "caisse" => $total_caisse["base"]);
$soustotal_dh   = array("tarmed" => $total_tarmed["dh"], "caisse" => $total_caisse["dh"]);
$total["tarmed"] = round($total_tarmed["base"]+$total_tarmed["dh"], 2);
$total["caisse"] = round($total_caisse["base"]+$total_caisse["dh"], 2);

// Vérification de la check list journalière
$daily_check_list = CDailyCheckList::getList($salle, $date);
$daily_check_list->loadItemTypes();
$daily_check_list->loadBackRefs('items');

$cat = new CDailyCheckItemCategory;
$cat->target_class = "CSalle";
$daily_check_item_categories = $cat->loadMatchingList();

// Chargement des 3 check lists de l'OMS
$operation_check_lists = array();
$operation_check_item_categories = array();

$operation_check_list = new CDailyCheckList;
$cat = new CDailyCheckItemCategory;
$cat->target_class = "COperation";

// Pre-anesth, pre-op, post-op
foreach ($operation_check_list->_specs["type"]->_list as $type) {
  $list = CDailyCheckList::getList($selOp, null, $type);
  $list->loadItemTypes();
  $list->loadRefsFwd();
  $list->loadBackRefs('items');
  $list->isReadonly();
  $list->_ref_object->loadRefPraticien();
  $operation_check_lists[$type] = $list;
  
  $cat->type = $type;
  $operation_check_item_categories[$type] = $cat->loadMatchingList("title");
}

$anesth = new CMediusers();
$anesth->load($anesth_id);

// Création du template
$smarty = new CSmartyDP();

if ($selOp->_id) {
  $smarty->assign("listChamps", $listChamps);
}

$group = CGroups::loadCurrent();
$group->loadConfigValues();

$listValidateurs = CPersonnel::loadListPers(array("op", "op_panseuse"), true, true);
$operateurs_disp_vasc = implode("-", array_merge(CMbArray::pluck($listChirs, "_id"), CMbArray::pluck($listValidateurs, "user_id")));

// Lib Flot pour les graphiques de surveillance perop
if (CAppUI::conf("dPsalleOp enable_surveillance_perop")) {
  CSupervisionGraph::includeFlot();
}

$smarty->assign("soustotal_base" , $soustotal_base);
$smarty->assign("soustotal_dh"   , $soustotal_dh);
$smarty->assign("total"          , $total);

$smarty->assign("anesth_perop"           , new CAnesthPerop());
$smarty->assign("unites"                 , $unites);
$smarty->assign("acte_ngap"              , $acte_ngap);
$smarty->assign("acte_tarmed"            , $acte_tarmed);
$smarty->assign("acte_caisse"            , $acte_caisse);
$smarty->assign("op"                     , $op);
$smarty->assign("salle"                  , $salle->_id);
$smarty->assign("currUser"               , $currUser);
$smarty->assign("listAnesthType"         , $listAnesthType);
$smarty->assign("listAnesths"            , $listAnesths);
$smarty->assign("listChirs"              , $listChirs);
$smarty->assign("operateurs_disp_vasc"   , $operateurs_disp_vasc);
$smarty->assign("modeDAS"                , CAppUI::conf("dPsalleOp CDossierMedical DAS"));
$smarty->assign("selOp"                  , $selOp);
$smarty->assign("date"                   , $date);
$smarty->assign("modif_operation"        , $modif_operation);
$smarty->assign("listValidateurs"        , $listValidateurs);
$smarty->assign("isPrescriptionInstalled", CModule::getActive("dPprescription"));
$smarty->assign("isbloodSalvageInstalled", CModule::getActive("bloodSalvage"));
$smarty->assign("isImedsInstalled"       , (CModule::getActive("dPImeds") && CImeds::getTagCIDC(CGroups::loadCurrent())));
$smarty->assign("codage_prat"            , $group->_configs["codage_prat"]);
$smarty->assign("_is_dentiste"           , $selOp->_ref_chir->isDentiste());
if (CModule::getActive("dPprescription")) {
  if (!isset($prescription)) {
    $prescription = new CPrescription();
  }
  $smarty->assign("prescription"           , $prescription);
}
$smarty->assign("protocoles"             , $protocoles);
$smarty->assign("anesth_id"              , $anesth_id);
$smarty->assign("anesth"                 , $anesth);
$smarty->assign("user_id"                , $user->_id);
$smarty->assign("create_dossier_anesth"  , 1);

// Check lists
$smarty->assign("daily_check_list"               , $daily_check_list);
$smarty->assign("daily_check_item_categories"    , $daily_check_item_categories);
$smarty->assign("operation_check_lists"          , $operation_check_lists);
$smarty->assign("operation_check_item_categories", $operation_check_item_categories);

if (CModule::getActive("maternite") && $selOp->_id) {
  $smarty->assign("naissance"            , new CNaissance);
}

$smarty->display("inc_operation.tpl");
