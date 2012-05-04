<?php /* $Id ajax_creation_facture.php $ */

/**
 * @package Mediboard
 * @subpackage dPcabinet
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$patient_id     = CValue::getOrSession("patient_id");
$du_patient     = CValue::get("du_patient");
$ajout_consult  = CValue::get("ajout_consult");
$consult_id     = CValue::get("consult_id");
$du_tiers       = CValue::get("du_tiers", "0");
$type_facture   = CValue::get("type_facture", "maladie");
$chirsel_id     = CValue::get("executant_id");
$date           = CValue::get("date", mbDate());

$facture = new CFactureConsult();

//Si la facture existe déjà on la met à jour
$where["patient_id"]  = "= '$patient_id'";
$where["cloture"]     = "IS NULL";

if($facture->loadObject($where)){
  $facture->loadRefsConsults();
  if($du_patient && !$ajout_consult){
    $somme = 0;
    $somme2 = 0;
    foreach($facture->_ref_consults as $consultation){
      $somme  += $consultation->du_patient;
      $somme2 += $consultation->du_tiers;
    }
    
    $facture->du_patient = $somme + $du_patient;
    $facture->du_tiers   = $somme2 + $du_tiers;
    $facture->tarif = null;
    $facture->store();
  }
  elseif($du_patient && $ajout_consult){
  	$consult = new CConsultation();
  	$consult->load($consult_id);
  	$consult->valide = 0;
  	$consult->store();
  	
  	$acte_tarmed = new CActeTarmed();
  	$acte_tarmed->object_id = $consult_id;
  	$acte_tarmed->object_class = 'CConsultation';
  	$acte_tarmed->executant_id = $chirsel_id;
  	if(CValue::get("_libelle") && !CValue::get("code")){
      $acte_tarmed->libelle = CValue::get("_libelle");
  	}
  	$acte_tarmed->date = $date;
    $acte_tarmed->code = CValue::get("code");
    $acte_tarmed->montant_base = CValue::get("montant_base");
    $acte_tarmed->quantite = CValue::get("quantite");
    $acte_tarmed->store();
    
  	$facture->du_patient = $facture->du_patient + $du_patient;
    $facture->du_tiers   = $facture->du_tiers + $du_tiers;
    $facture->tarif = null;
    $facture->store();
    
    $consult->valide = 1;
    $consult->store();
  }
}
//Sinon on la créé
else{
  $facture->patient_id    = $patient_id;
  $facture->du_patient    = $du_patient;
  $facture->du_tiers      = $du_tiers;  
  $facture->type_facture  = $type_facture;  
  $facture->ouverture     = mbDate();
}

//Enregistrement des modifications si besoin
if($facture->du_patient){
  $facture->store();
  
  //Chargement des éléments de la facture
  $consultation = new CConsultation();
  $consultation->load($consult_id);
  
  //Ajout de l'id de la facture dans la consultation
  if($facture->_id){
    $consultation->factureconsult_id = $facture->_id;
    $consultation->store();
  }
  $facture->loadRefs();
}

$acte_tarmed = null;
//Instanciation d'un acte tarmed pour l'ajout de ligne dans la facture
if(CModule::getInstalled("tarmed") && CAppUI::conf("tarmed CCodeTarmed use_cotation_tarmed") ){
  $acte_tarmed = new CActeTarmed();
  $acte_tarmed->date = mbDate();
  $acte_tarmed->quantite = 1;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("acte_tarmed"   , $acte_tarmed);
$smarty->assign("facture"       , $facture);
if(!CValue::get("not_load_banque")){
  $smarty->assign("factures"      , array(new CFactureConsult()));
}
$smarty->assign("derconsult_id" , $consult_id);
$smarty->assign("date"          , $date);
$smarty->assign("chirSel"       , $chirsel_id);

$smarty->display("inc_vw_facturation.tpl");
?>