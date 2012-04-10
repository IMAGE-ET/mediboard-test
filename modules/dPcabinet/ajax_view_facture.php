<?php

CCanDo::checkEdit();

$factureconsult_id  = CValue::getOrSession("factureconsult_id");
$patient_id         = CValue::getOrSession("patient_id");
$consult_id         = CValue::get("consult_id");

$derconsult_id = null;
$factures= array();
$facture = new CFactureConsult();

if($consult_id){
	if($facture->loadObject("patient_id = '$patient_id' AND cloture IS NULL")){
	  $facture->loadRefs();
	  if(count($facture->_ref_consults) == 0){
	    $facture->delete();
	  }
	  else{
	    $somme  = 0;
	    $somme2 = 0;
	    foreach($facture->_ref_consults as $consultation){
	      $somme  += $consultation->du_patient;
	      $somme2 += $consultation->du_tiers;
	    }
	    if($somme != $facture->du_patient || $somme2 != $facture->du_tiers){
	      $facture->du_patient = $somme ;
	      $facture->du_tiers   = $somme2;
	      $facture->store();
	    }
	  }
	}
	else if($factures = $facture->loadList("patient_id = '$patient_id' AND cloture IS NOT NULL")){
	  foreach($factures as $_facture){
	    $_facture->loadRefsConsults();
	    foreach($_facture->_ref_consults as $consultation){
	      if($consultation->_id == $consult_id){
	        $facture = $_facture;
	        $facture->loadRefPatient();
	        $facture->loadRefReglements();
	      }
	    }
	  }  
	}
}
else{
	if($factureconsult_id){
	  $facture->load($factureconsult_id); 
	  $facture->loadRefs();
	}
	if($facture->_ref_consults){
		$last_consult = reset($facture->_ref_consults);
    $derconsult_id = $last_consult->_id;
	}
}

// Chargement des banques si nous sommes dans la vue des factures
$reglement = null;
$banques = null;
if(!CValue::get("not_load_banque")){
  $reglement   = new CReglement();
  $orderBanque = "nom ASC";
  $banque      = new CBanque();
  $banques     = $banque->loadList(null,$orderBanque);
  $factures[0] = "xpi";
}

$acte_tarmed = null;
//Instanciation d'un acte tarmed pour l'ajout de ligne dans la facture
if(CModule::getInstalled("tarmed") && CAppUI::conf("tarmed CCodeTarmed use_cotation_tarmed") ){
  $acte_tarmed = new CActeTarmed();
  $acte_tarmed->date     = mbDate();
  $acte_tarmed->quantite = 1;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("facture"       , $facture);
$smarty->assign("reglement"     , $reglement);
$smarty->assign("acte_tarmed"   , $acte_tarmed);
$smarty->assign("derconsult_id" , $derconsult_id);
$smarty->assign("banques"       , $banques);
$smarty->assign("factures"      , $factures);
$smarty->assign("etat_ouvert"   , CValue::getOrSession("etat_ouvert", 1));
$smarty->assign("etat_cloture"  , CValue::getOrSession("etat_cloture", 1));
$smarty->assign("date"          , mbDate());
$smarty->assign("chirSel"       , CValue::getOrSession("chirSel", "-1"));

$smarty->display("inc_vw_facturation.tpl");
?>