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
$offline = CValue::get("offline");

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
  $listAnesths = new CMediusers;
  $listAnesths = $listAnesths->loadAnesthesistes(PERM_READ);
  
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
  $consult->loadRefsExamIgs();
  $consult->loadRefSejour();
  
  if($consult->_ref_consult_anesth->_id) {
    $consult_anesth = $consult->_ref_consult_anesth;
    $consult_anesth->loadRefs();
    $consult_anesth->_ref_sejour->loadRefDossierMedical();
    
		if (Cmodule::getActive("dPprescription")){
			// Chargement de toutes les planifs systemes si celles-ci ne sont pas deja charg�es
	    $consult_anesth->_ref_sejour->loadRefPrescriptionSejour();
			$consult_anesth->_ref_sejour->_ref_prescription_sejour->calculAllPlanifSysteme();
			
	    // R�cup�ration des planifications syst�mes ant�rieures � l'intervention
	    $planif_system = new CPlanificationSysteme();
	    $where  = array();
	    $where["sejour_id"] = " = '{$consult_anesth->_ref_sejour->_id}'";
	    $where["dateTime"] = " < '{$consult_anesth->_ref_operation->_datetime}'";
	    $where["object_class"] = " NOT LIKE 'CPrescriptionLineElement'";
	    
	    $list_planifs_system = $planif_system->loadlist($where);
	    
	    foreach ($list_planifs_system as $_planif) {
	      $_planif->loadRefPrise();
	      $_planif->loadTargetObject();
	      $object = $_planif->_ref_object;
	      
	      if ($object instanceof CPrescriptionLineMedicament) {
	        $_planif->_ref_prise->loadRefsFwd();
	        $object->_ref_prises = array();
	        $object->_ref_prises[$_planif->_ref_prise->_id] = $_planif->_ref_prise;
	      }
	      
	      if ($object instanceof CPrescriptionLineMixItem) {
	        $object->loadRefPerfusion();
	        $object->_ref_prescription_line_mix->loadRefPraticien();
	        $object->_ref_prescription_line_mix->_ref_lines = array();
	        
	        // Il faut cloner $object pour casser la r�f�rence lors de l'�crasement � la ligne suivante
	        $object->_ref_prescription_line_mix->_ref_lines[$object->_id] = clone $object;
	        $object = $object->_ref_prescription_line_mix;
	      }
	      
	      $lines[$object->_guid] = $object;
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

// Affichage des donn�es
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

//Tableau d'unit�s
$unites = array();
$unites["hb"]           = array("nom"=>"Hb","unit"=>"g/dl");
$unites["ht"]           = array("nom"=>"Ht","unit"=>"%");
$unites["ht_final"]     = array("nom"=>"Ht final","unit"=>"%");
$unites["plaquettes"]   = array("nom"=>"Plaquettes","unit"=>"(x1000) /mm3");
$unites["creatinine"]   = array("nom"=>"Cr�atinine","unit"=>"mg/l");
$unites["_clairance"]   = array("nom"=>"Clairance de Cr�atinine","unit"=>"ml/min");
$unites["fibrinogene"]  = array("nom"=>"Fibrinog�ne","unit"=>"g/l");
$unites["na"]           = array("nom"=>"Na+","unit"=>"mmol/l");
$unites["k"]            = array("nom"=>"K+","unit"=>"mmol/l");
$unites["tp"]           = array("nom"=>"TP","unit"=>"%");
$unites["tca"]          = array("nom"=>"TCA","unit"=>"s");
$unites["tsivy"]        = array("nom"=>"TS Ivy","unit"=>"");
$unites["ecbu"]         = array("nom"=>"ECBU","unit"=>"");
$unites["date_analyse"] = array("nom"=>"Date","unit"=>"");

// Cr�ation du template
$smarty = new CSmartyDP("modules/dPcabinet");

$smarty->assign("offline"   , $offline);
$smarty->assign("unites"    , $unites);
$smarty->assign("listChamps", $listChamps);
$smarty->assign("consult"   , $consult);
$smarty->assign("etatDents" , $sEtatsDents);
$smarty->assign("print"     , $print);
$smarty->assign("praticien" , new CUser);
$smarty->assign("lines"     , $lines);
$smarty->assign("dossier_medical_sejour", $consult->_ref_consult_anesth->_ref_sejour->_ref_dossier_medical);
$template = CAppUI::conf("dPcabinet CConsultAnesth feuille_anesthesie");

$smarty->display($template.".tpl");
?>