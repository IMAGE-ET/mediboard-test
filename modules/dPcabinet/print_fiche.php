<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::check();

if (!CModule::getCanDo('dPcabinet')->edit && !CModule::getCanDo('soins')->read) {
  CModule::getCanDo('dPcabinet')->redirect();
}

$date  = CValue::getOrSession("date", CMbDT::date());
$print = CValue::getOrSession("print", false);
$today = CMbDT::date();

$dossier_anesth_id     = CValue::get("dossier_anesth_id");
$operation_id          = CValue::getOrSession("operation_id");
$create_dossier_anesth = CValue::get("create_dossier_anesth", 1);
$multi                 = CValue::get("multi");
$offline               = CValue::get("offline");
$display               = CValue::get("display");
$pdf                   = CValue::get("pdf", 1);
$lines        = array();
$lines_per_op = array();

// Consultation courante
$dossier_anesth = new CConsultAnesth();
if (!$dossier_anesth_id) {
  $where = array();
  $where["operation_id"] = " = '$operation_id'";
  $dossier_anesth->loadObject($where);
}
else {
  $dossier_anesth->load($dossier_anesth_id);
}

if (!$dossier_anesth->_id) {
  $selOp = new COperation();
  $selOp->load($operation_id);
  $selOp->loadRefsFwd();
  $selOp->_ref_sejour->loadRefsFwd();
  $selOp->_ref_sejour->loadRefsConsultAnesth();
  $selOp->_ref_sejour->_ref_consult_anesth->loadRefsFwd();

  $patient = $selOp->_ref_sejour->_ref_patient;
  $patient->loadRefsConsultations();

  // Chargement des praticiens
  $listAnesths = array();
  if ($offline == "false") {
    $anesths = new CMediusers();
    $listAnesths = $anesths->loadAnesthesistes(PERM_READ);
  }

  foreach ($patient->_ref_consultations as $consultation) {
    $consultation->loadRefConsultAnesth();

    foreach ($consultation->_refs_dossiers_anesth as $_dossier_anesth) {
      $consultation->loadRefPlageConsult();
      $_dossier_anesth->loadRefOperation();
    }
  }

  $onSubmit = "return onSubmitFormAjax(this,
    window.refreshFicheAnesth ||
    function(){
      window.opener.chooseAnesthCallback.defer(); window.close();
    }
  )";

  $smarty = new CSmartyDP("modules/dPcabinet");

  $smarty->assign("selOp"                , $selOp);
  $smarty->assign("patient"              , $patient);
  $smarty->assign("listAnesths"          , $listAnesths);
  $smarty->assign("onSubmit"             , $onSubmit);
  $smarty->assign("create_dossier_anesth", $create_dossier_anesth);

  $smarty->display("inc_choose_dossier_anesth.tpl");

  return;
}

$dossier_anesth->loadRefsDocs();
$consult = $dossier_anesth->loadRefConsultation();
$consult->loadRefPlageConsult();

if ($pdf) {
  // Si le modèle est redéfini, on l'utilise
  $model = CCompteRendu::getSpecialModel($consult->_ref_chir, "CConsultAnesth", "[FICHE ANESTH]");

  if ($model->_id) {
    CCompteRendu::streamDocForObject($model, $dossier_anesth, $model->factory);
  }
}

$consult->loadRefsFwd();
$consult->loadRefsDossiersAnesth();
$consult->loadRefsExamsComp();
$consult->loadRefsExamNyha();
$consult->loadRefsExamPossum();

$dossier_anesth->loadRefs();
$dossier_anesth->_ref_sejour->loadRefDossierMedical();

$other_intervs = array();
$pos_curr_interv = 0;

foreach ($consult->loadRefsDossiersAnesth() as $_dossier_anesth) {
  if ($_dossier_anesth->operation_id) {
    $_op = $_dossier_anesth->loadRefOperation();
    $_op->loadRefPlageOp();
    $_op->loadRefChir();
    $other_intervs[$_op->_id] = $_op;
  }
}

ksort($other_intervs);

if (count($other_intervs) > 1) {
  $pos_curr_interv = array_search($dossier_anesth->operation_id, array_keys($other_intervs));
  $pos_curr_interv++;
}

// Lignes de prescription en prémédication
if (CModule::getActive("dPprescription")) {
  $prescription = $dossier_anesth->_ref_sejour->loadRefPrescriptionSejour();
  $prescription->loadRefsLinesElement();
  $prescription->loadRefsLinesMed();
  $prescription->loadRefsPrescriptionLineMixes();
  $prescription->loadRefsLinesPerop();

  $show_premed_chir_fiche = CAppUI::conf("dPprescription CPrescription show_premed_chir_fiche", CGroups::loadCurrent());

  foreach ($prescription->_ref_prescription_lines_element as $_line_elt) {
    if (!$_line_elt->premedication) {
      continue;
    }
    if (!$show_premed_chir_fiche && !$_line_elt->_ref_praticien->isAnesth()) {
      continue;
    }
    $_line_elt->loadRefsPrises();
    $lines[] = $_line_elt;
  }

  foreach ($prescription->_ref_prescription_lines as $_line_med) {
    if (!$_line_med->premedication) {
      continue;
    }
    if (!$show_premed_chir_fiche && !$_line_med->_ref_praticien->isAnesth()) {
      continue;
    }
    $_line_med->loadRefsPrises();
    $_line_med->loadRefMomentArret();
    $lines[] = $_line_med;
  }

  foreach ($prescription->_ref_prescription_line_mixes as $_line_mix) {
    if (!$_line_mix->premedication) {
      continue;
    }
    $_line_mix->loadRefPraticien();
    if (!$show_premed_chir_fiche && !$_line_mix->_ref_praticien->isAnesth()) {
      continue;
    }
    $_line_mix->loadRefsLines();
    $lines[] = $_line_mix;
  }
  foreach ($prescription->_ref_lines_perop as $type => $tab_line) {
    foreach ($tab_line as $_line_per_op) {
      if ($type == "med" || $type == "elt") {
        /* @var CPrescriptionLine $_line_per_op*/
        $_line_per_op->loadRefsPrises();
      }
      if ($type == "mix") {
        /* @var CPrescriptionLineMix $_line_per_op*/
        $_line_per_op->loadRefPraticien();
        $_line_per_op->loadRefsLines();
      }

      $lines_per_op[] = $_line_per_op;
    }
  }
}

$praticien =& $consult->_ref_chir;
$patient   =& $consult->_ref_patient;
$patient->loadRefDossierMedical();
$patient->loadIPP();

$dossier_medical =& $patient->_ref_dossier_medical;

$patient->loadRefsConsultations();
$dossiers = array();
foreach ($patient->_ref_consultations as $consultation) {
  $consultation->loadRefConsultAnesth();
  $consultation->loadRefPlageConsult();
  foreach ($consultation->_refs_dossiers_anesth as $_dossier_anesth) {
    $_dossier_anesth->_ref_consultation = $consultation;
    $_dossier_anesth->loadRefOperation();
    $dossiers[$_dossier_anesth->_id] = $_dossier_anesth;
  }
}

// Chargement des elements du dossier medical
$dossier_medical->loadRefsAntecedents();
$dossier_medical->countAllergies();
$dossier_medical->loadRefsTraitements();
$dossier_medical->loadRefsEtatsDents();
$dossier_medical->loadRefPrescription();
if ($dossier_medical->_ref_prescription && $dossier_medical->_ref_prescription->_id) {
  foreach ($dossier_medical->_ref_prescription->_ref_prescription_lines as $_line) {
    if ($_line->fin && $_line->fin <= CMbDT::date()) {
      unset($dossier_medical->_ref_prescription->_ref_prescription_lines[$_line->_id]);
    }
    $_line->loadRefsPrises();
  }
}
$etats = array();
if (is_array($dossier_medical->_ref_etats_dents)) {
  foreach ($dossier_medical->_ref_etats_dents as $etat) {
    if ($etat->etat != null) {
      switch ($etat->dent) {
        case 10:
        case 50:
          $position = "Central haut";
          break;
        case 30:
        case 70:
          $position = "Central bas";
          break;
        default:
          $position = $etat->dent;
      }
      if (!isset ($etats[$etat->etat])) {
        $etats[$etat->etat] = array();
      }
      $etats[$etat->etat][] = $position;
    }
  }
}
$sEtatsDents = "";
foreach ($etats as $key => $list) {
  sort($list);
  $sEtatsDents .= "- " . ucfirst($key) . " : " . implode(", ", $list) . "\n";
}

// Affichage des données
$listChamps = array(
  1 => array("date_analyse", "hb", "ht", "ht_final", "plaquettes"),
  2 => array("creatinine", "_clairance", "fibrinogene", "na", "k"),
  3 => array("tp", "tca", "tsivy", "ecbu")
);

foreach ($listChamps as $keyCol => $aColonne) {
  foreach ($aColonne as $keyChamp => $champ) {
    $verifchamp = true;
    if ($champ == "tca") {
      $champ2 = $dossier_anesth->tca_temoin;
    }
    else {
      $champ2 = false;
      if (($champ == "ecbu" && $dossier_anesth->ecbu == "?") || ($champ == "tsivy" && $dossier_anesth->tsivy == "00:00:00")) {
        $verifchamp = false;
      }
    }
    $champ_exist = $champ2 || ($verifchamp && $dossier_anesth->$champ);
    if (!$champ_exist) {
      unset($listChamps[$keyCol][$keyChamp]);
    }
  }
}

// Tableau d'unités
$unites                 = array();
$unites["hb"]           = array("nom" => "Hb", "unit" => "g/dl");
$unites["ht"]           = array("nom" => "Ht", "unit" => "%");
$unites["ht_final"]     = array("nom" => "Ht final", "unit" => "%");
$unites["plaquettes"]   = array("nom" => "Plaquettes", "unit" => "(x1000) /mm3");
$unites["creatinine"]   = array("nom" => "Créatinine", "unit" => "mg/l");
$unites["_clairance"]   = array("nom" => "Clairance de Créatinine", "unit" => "ml/min");
$unites["_clairance"]   = array("nom" => "Clairance de Créatinine", "unit" => "ml/min");
$unites["fibrinogene"]  = array("nom" => "Fibrinogène", "unit" => "g/l");
$unites["na"]           = array("nom" => "Na+", "unit" => "mmol/l");
$unites["k"]            = array("nom" => "K+", "unit" => "mmol/l");
$unites["tp"]           = array("nom" => "TP", "unit" => "%");
$unites["tca"]          = array("nom" => "TCA", "unit" => "s");
$unites["tsivy"]        = array("nom" => "TS Ivy", "unit" => "");
$unites["ecbu"]         = array("nom" => "ECBU", "unit" => "");
$unites["date_analyse"] = array("nom" => "Date", "unit" => "");

// Création du template
$smarty = new CSmartyDP("modules/dPcabinet");

$smarty->assign("dossiers"      , $dossiers);
$smarty->assign("display"       , $display);
$smarty->assign("offline"       , $offline);
$smarty->assign("unites"        , $unites);
$smarty->assign("listChamps"    , $listChamps);
$smarty->assign("dossier_anesth", $dossier_anesth);
$smarty->assign("etatDents"     , $sEtatsDents);
$smarty->assign("print"         , $print);
$smarty->assign("praticien"     , new CUser());
$smarty->assign("lines"         , $lines);
$smarty->assign("lines_per_op"  , $lines_per_op);
$smarty->assign("multi"         , $multi);
$smarty->assign("dossier_medical_sejour", $dossier_anesth->_ref_sejour->_ref_dossier_medical);
$smarty->assign("other_intervs" , $other_intervs);
$smarty->assign("pos_curr_interv", $pos_curr_interv);

$smarty->display(CAppUI::conf("dPcabinet CConsultAnesth feuille_anesthesie").".tpl");
