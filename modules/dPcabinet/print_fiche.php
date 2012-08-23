<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

if(!CModule::getCanDo('dPcabinet')->edit && !CModule::getCanDo('soins')->read){
  CModule::getCanDo('dPcabinet')->redirect();
}

//CCanDo::checkEdit();

$date = CValue::getOrSession("date", mbDate());
$print = CValue::getOrSession("print", false);
$today = mbDate();

$consultation_id       = CValue::get("consultation_id");
$operation_id          = CValue::get("operation_id");
$create_dossier_anesth = CValue::get("create_dossier_anesth", 0);
$multi   = CValue::get("multi");
$offline = CValue::get("offline");
$display = CValue::get("display");

$lines = array();

// Consultation courante
$consult = new CConsultation();

if(!$consultation_id) {

  $selOp = new COperation();
  $selOp->load($operation_id);
  $selOp->loadRefsFwd();
  $selOp->_ref_sejour->loadRefsFwd();
  $selOp->_ref_sejour->loadRefsConsultAnesth();
  $selOp->_ref_sejour->_ref_consult_anesth->loadRefsFwd();
  
  $patient = new CPatient();
  $patient = $selOp->_ref_sejour->_ref_patient;
  $patient->loadRefsConsultations();
  
  // Chargement des praticiens
  $listAnesths = array();
  if (!$offline) {
    $listAnesths = new CMediusers;
    $listAnesths = $listAnesths->loadAnesthesistes(PERM_READ);
  }
  
  foreach ($patient->_ref_consultations as $consultation) {
    $consultation->loadRefConsultAnesth();
    $consult_anesth =& $consultation->_ref_consult_anesth;
    if ($consult_anesth->_id) {
      $consultation->loadRefPlageConsult();
      $consult_anesth->loadRefOperation();
    }
  }
  
  $onSubmit = "return onSubmitFormAjax(this, { onComplete : function() {window.opener.chooseAnesthCallback.defer(); window.close();} })";

  $smarty = new CSmartyDP("modules/dPcabinet");
  
  $smarty->assign("selOp"                , $selOp);
  $smarty->assign("patient"              , $patient);
  $smarty->assign("listAnesths"          , $listAnesths);
  $smarty->assign("onSubmit"             , $onSubmit);
  $smarty->assign("create_dossier_anesth", $create_dossier_anesth);

  $smarty->display("inc_choose_dossier_anesth.tpl");
  
  return;
}

if ($consultation_id) {
  $consult->load($consultation_id);
  $consult->loadRefsDocs();
  $consult->loadRefConsultAnesth();
  $consult->loadRefsFwd();
  $consult->loadExamsComp();
  $consult->loadRefsExamNyha();
  $consult->loadRefsExamPossum();
  $consult->loadRefSejour();
  
  if($consult->_ref_consult_anesth->_id) {
    $consult_anesth = $consult->_ref_consult_anesth;
    $consult_anesth->loadRefs();
    $consult_anesth->_ref_sejour->loadRefDossierMedical();
    
    // Lignes de prescription en prémédication    
    if (CModule::getActive("dPprescription")) {
    $prescription = $consult_anesth->_ref_sejour->loadRefPrescriptionSejour();
      $prescription->loadRefsLinesElement();
      $prescription->loadRefsLinesMed();
      $prescription->loadRefsPrescriptionLineMixes();
      
      foreach ($prescription->_ref_prescription_lines_element as $_line_elt) {
        if (!$_line_elt->premedication) {
          continue;
        }
        $_line_elt->loadRefsPrises();
        $lines[] = $_line_elt;
      }
      
      foreach ($prescription->_ref_prescription_lines as $_line_med) {
        if (!$_line_med->premedication) {
          continue;
        }
        $_line_med->loadRefsPrises();
        $lines[] = $_line_med;
      }
      
      foreach ($prescription->_ref_prescription_line_mixes as $_line_mix) {
        if (!$_line_mix->premedication) {
          continue;
        }
        $_line_mix->loadRefPraticien();
        $_line_mix->loadRefsLines();
        $lines[] = $_line_mix;
      }
    }
  }

  $praticien =& $consult->_ref_chir;
  $patient   =& $consult->_ref_patient;
  $patient->loadRefDossierMedical();
  $dossier_medical =& $patient->_ref_dossier_medical;
  
  // Chargement des elements du dossier medical
  $dossier_medical->loadRefsAntecedents();
  $dossier_medical->countAllergies();
  $dossier_medical->loadRefsTraitements();
  $dossier_medical->loadRefsEtatsDents();
  $dossier_medical->loadRefPrescription();
  if($dossier_medical->_ref_prescription && $dossier_medical->_ref_prescription->_id){
    foreach($dossier_medical->_ref_prescription->_ref_prescription_lines as $_line){
      if($_line->fin && $_line->fin <= mbDate()){
        unset($dossier_medical->_ref_prescription->_ref_prescription_lines[$_line->_id]);
      }
      $_line->loadRefsPrises();
    }
  }
  $etats = array();
  if (is_array($dossier_medical->_ref_etats_dents)) {
    foreach($dossier_medical->_ref_etats_dents as $etat) {
      if ($etat->etat != null) {
        switch ($etat->dent) {
          case 10: 
          case 30: $position = "Central haut"; break;
          case 50: 
          case 70: $position = "Central bas"; break;
          default: $position = $etat->dent;
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
    $sEtatsDents .= "- ".ucfirst($key)." : ".implode(", ", $list)."\n";
  }
}

// Affichage des données
$listChamps = array(
                1=>array("date_analyse","hb","ht","ht_final","plaquettes"),
                2=>array("creatinine","_clairance","fibrinogene","na","k"),
                3=>array("tp","tca","tsivy","ecbu")
                );
$cAnesth =& $consult->_ref_consult_anesth;
foreach($listChamps as $keyCol=>$aColonne){
  foreach($aColonne as $keyChamp=>$champ){
    $verifchamp = true;
    if($champ=="tca"){
      $champ2 = $cAnesth->tca_temoin;
    }else{
      $champ2 = false;
      if(($champ=="ecbu" && $cAnesth->ecbu=="?") || ($champ=="tsivy" && $cAnesth->tsivy=="00:00:00")){
        $verifchamp = false;
      }
    }
    $champ_exist = $champ2 || ($verifchamp && $cAnesth->$champ);
    if(!$champ_exist){
      unset($listChamps[$keyCol][$keyChamp]);
    }
  }
}

//Tableau d'unités
$unites = array();
$unites["hb"]           = array("nom"=>"Hb","unit"=>"g/dl");
$unites["ht"]           = array("nom"=>"Ht","unit"=>"%");
$unites["ht_final"]     = array("nom"=>"Ht final","unit"=>"%");
$unites["plaquettes"]   = array("nom"=>"Plaquettes","unit"=>"(x1000) /mm3");
$unites["creatinine"]   = array("nom"=>"Créatinine","unit"=>"mg/l");
$unites["_clairance"]   = array("nom"=>"Clairance de Créatinine","unit"=>"ml/min");
$unites["fibrinogene"]  = array("nom"=>"Fibrinogène","unit"=>"g/l");
$unites["na"]           = array("nom"=>"Na+","unit"=>"mmol/l");
$unites["k"]            = array("nom"=>"K+","unit"=>"mmol/l");
$unites["tp"]           = array("nom"=>"TP","unit"=>"%");
$unites["tca"]          = array("nom"=>"TCA","unit"=>"s");
$unites["tsivy"]        = array("nom"=>"TS Ivy","unit"=>"");
$unites["ecbu"]         = array("nom"=>"ECBU","unit"=>"");
$unites["date_analyse"] = array("nom"=>"Date","unit"=>"");

// Création du template
$smarty = new CSmartyDP("modules/dPcabinet");

$smarty->assign("display"   , $display);
$smarty->assign("offline"   , $offline);
$smarty->assign("unites"    , $unites);
$smarty->assign("listChamps", $listChamps);
$smarty->assign("consult"   , $consult);
$smarty->assign("etatDents" , $sEtatsDents);
$smarty->assign("print"     , $print);
$smarty->assign("praticien" , new CUser);
$smarty->assign("lines"     , $lines);
$smarty->assign("multi"     , $multi);
$smarty->assign("dossier_medical_sejour", $consult->_ref_consult_anesth->_ref_sejour->_ref_dossier_medical);
$template = CAppUI::conf("dPcabinet CConsultAnesth feuille_anesthesie");

$smarty->display($template.".tpl");
?>