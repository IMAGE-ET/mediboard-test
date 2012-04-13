<?php /* $Id vw_factures.php $ */

/**
 * @package Mediboard
 * @subpackage dPcabinet
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

$etat               = CValue::getOrSession("etat", "ouvert");
$etat_cloture       = CValue::getOrSession("etat_cloture", 1);
$etat_ouvert        = CValue::getOrSession("etat_ouvert", 1);
$factureconsult_id  = CValue::getOrSession("factureconsult_id");
$patient_id         = CValue::getOrSession("patient_id");
$no_finish_reglement= CValue::getOrSession("no_finish_reglement", 0);

// Praticien selectionné
$chirSel = CValue::getOrSession("chirSel", "-1");

//Patient sélectionné
$patient = new CPatient();
$patient->load($patient_id);

// Liste des chirurgiens
$user = new CMediusers();
$listChir =  $user->loadPraticiens(PERM_EDIT) ;

$cloture = "NULL";
if($etat_cloture){
	$cloture = "NOT NULL";
}

//Tri des factures ayant un chir dans une de ses consultations
$factures= array();
$facture = new CFactureConsult();

if ($chirSel){
	$ljoin = array();
	$where = array();
	
	$ljoin["consultation"] = "factureconsult.factureconsult_id = consultation.factureconsult_id" ;
	$ljoin["plageconsult"] = "consultation.plageconsult_id = plageconsult.plageconsult_id" ;
	
	$where["consultation.factureconsult_id"] =" IS NOT NULL ";
	$where["plageconsult.chir_id"] =" = '$chirSel' ";
	
	if(!($etat_ouvert && $etat_cloture)){
		$where["factureconsult.cloture"] =" IS $cloture ";
	}
	if($patient_id){
		$where["factureconsult.patient_id"] =" = '$patient_id' ";
	}
	
	$order = "factureconsult.cloture DESC";
	$limit ="0 , 30";
	
	$facture = new CFactureConsult();
	$factures = $facture->loadList($where, $order, $limit, null, $ljoin);
}
else{
	$factures = $facture->loadList("cloture IS $cloture AND patient_id = '$patient_id' ", "ouverture ASC", 50);
}

if($no_finish_reglement){
	foreach($factures as $key => $_facture){
	  $_facture->loadRefReglements();
	  if($_facture->_du_patient_restant==0 ){
	    unset($factures[$key]);
	  }
	}
}

$derconsult_id = null;
if($factureconsult_id){
  $facture->load($factureconsult_id);	
  $facture->loadRefs();
  if($facture->_ref_consults){
  	$last_consult = reset($facture->_ref_consults);
    $derconsult_id = $last_consult->_id;
  }
  
}

// Chargement des banques si nous sommes dans la vue des factures
$reglement = null;
$banques = null;
if(!CValue::get("not_load_banque")){
  $reglement = new CReglement();
  $orderBanque = "nom ASC";
  $banque = new CBanque();
  $banques = $banque->loadList(null,$orderBanque);
}

$acte_tarmed = null;
//Instanciation d'un acte tarmed pour l'ajout de ligne dans la facture
if(CModule::getInstalled("tarmed") && CAppUI::conf("tarmed CCodeTarmed use_cotation_tarmed")){
	$acte_tarmed = new CActeTarmed();
	$acte_tarmed->date = mbDate();
	$acte_tarmed->quantite = 1;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("factures"      , $factures);
$smarty->assign("reglement"     , $reglement);
$smarty->assign("acte_tarmed"   , $acte_tarmed);
$smarty->assign("derconsult_id" , $derconsult_id);
$smarty->assign("listChirs"     , $listChir);
$smarty->assign("chirSel"       , $chirSel);
$smarty->assign("patient"       , $patient);
$smarty->assign("banques"       , $banques);
$smarty->assign("facture"       , $facture);
$smarty->assign("etat_ouvert"   , $etat_ouvert);
$smarty->assign("etat_cloture"  , $etat_cloture);
$smarty->assign("date"          , mbDate());
$smarty->assign("no_finish_reglement"      ,$no_finish_reglement);

$smarty->display("vw_factures.tpl");
?>