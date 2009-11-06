<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

if(!CModule::getCanDo('dPcabinet')->edit && !CModule::getCanDo('soins')->read){
  CModule::getCanDo('dPcabinet')->redirect();
}
//$can->needsEdit();

$date = CValue::getOrSession("date", mbDate());
$print = CValue::getOrSession("print", false);
$today = mbDate();

$consultation_id = CValue::get("consultation_id");

// Consultation courante
$consult = new CConsultation();
if ($consultation_id) {
  $consult->load($consultation_id);
  $consult->loadRefsDocs();
  $consult->loadRefConsultAnesth();
  $consult->loadRefsFwd();
  $consult->loadExamsComp();
  $consult->loadRefsExamNyha();
  $consult->loadRefsExamPossum();
  $consult->loadRefsExamIgs();
  
  if($consult->_ref_consult_anesth->consultation_anesth_id) {
    $consult->_ref_consult_anesth->loadRefs();
  }

  $praticien =& $consult->_ref_chir;
  $patient =& $consult->_ref_patient;
  $patient->loadRefDossierMedical();
  $dossier_medical =& $patient->_ref_dossier_medical;
  
  // Chargement des elements du dossier medical
  $dossier_medical->loadRefsAntecedents();
  $dossier_medical->loadRefsTraitements();
  $dossier_medical->loadRefsEtatsDents();
  $dossier_medical->loadRefPrescription();
  if($dossier_medical->_ref_prescription && $dossier_medical->_ref_prescription->_id){
	  foreach($dossier_medical->_ref_prescription->_ref_prescription_lines as $_line){
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
                2=>array("creatinine","_clairance","na","k"),
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
$unites["hb"]         = array("nom"=>"Hb","unit"=>"g/dl");
$unites["ht"]         = array("nom"=>"Ht","unit"=>"%");
$unites["ht_final"]   = array("nom"=>"Ht final","unit"=>"%");
$unites["plaquettes"] = array("nom"=>"Plaquettes","unit"=>"(x1000) /mm3");
$unites["creatinine"] = array("nom"=>"Créatinine","unit"=>"mg/l");
$unites["_clairance"] = array("nom"=>"Clairance de Créatinine","unit"=>"ml/min");
$unites["na"]         = array("nom"=>"Na+","unit"=>"mmol/l");
$unites["k"]          = array("nom"=>"K+","unit"=>"mmol/l");
$unites["tp"]         = array("nom"=>"TP","unit"=>"%");
$unites["tca"]        = array("nom"=>"TCA","unit"=>"s");
$unites["tsivy"]      = array("nom"=>"TS Ivy","unit"=>"");
$unites["ecbu"]       = array("nom"=>"ECBU","unit"=>"");
$unites["date_analyse"] = array("nom"=>"Date","unit"=>"");


// Création du template
$smarty = new CSmartyDP();

$smarty->assign("unites"    , $unites);
$smarty->assign("listChamps", $listChamps);
$smarty->assign("consult"   , $consult);
$smarty->assign("etatDents" , $sEtatsDents);
$smarty->assign("print", $print);
$template = CAppUI::conf("dPcabinet CConsultAnesth feuille_anesthesie");

$smarty->display($template.".tpl");
?>