<?php

/**
* @package Mediboard
* @subpackage dPsalleOp
* @version $Revision$
* @author Alexis Granger
*/

global $AppUI, $m;

$date = CValue::post("date");
$dialog = CValue::post("dialog");
$praticien_id = CValue::post("praticien_id");
$object_id = CValue::post("object_id");
$object_class = CValue::post("object_class");
$password = CValue::post("password");

if ($dialog){
  $redirectUrl = null;
} else {
  $redirectUrl = "m=dPsalleOp&tab=vw_signature_actes&date=$date";
}

// Chargement du praticien
$praticien = new CMediusers();
$praticien->load($praticien_id);

// Test du password en chargeant 
$user = new CUser();
$user->user_username = $praticien->_user_username;
$user->_user_password = $password;

if (!$user->_user_password) {
  CAppUI::setMsg("Veuillez saisir votre mot de passe", UI_MSG_ERROR );
  if ($redirectUrl) {
    CAppUI::redirect($redirectUrl);
  }
  CApp::rip();
}

$user->loadMatchingObject();

if (!$user->_id){
  CAppUI::setMsg("Mot de passe incorrect", UI_MSG_ERROR );
  if ($redirectUrl) {
    CAppUI::redirect($redirectUrl);
  }
  CApp::rip();
}

// Chargement des actes CCAM à modifier
$acte_ccam = new CActeCCAM();
$acte_ccam->object_id = $object_id;
$acte_ccam->object_class = $object_class;
$acte_ccam->executant_id = $user->_id;
$acte_ccam->signe = 0;
$actes_ccam = $acte_ccam->loadMatchingList();

// Modification des actes CCAM
foreach ($actes_ccam as $key => $_acte_ccam){
  $_acte_ccam->signe = 1;
  $msg = $_acte_ccam->store();
  if ($msg) {
  	CAppUI::setMsg($msg, UI_MSG_ERROR );
  }
}

// Transmission des actes CCAM lors de la signature
if (CAppUI::conf("dPpmsi transmission_actes") == "signature" && $object_class == "COperation") {
  $evenementActivitePMSI = new CHPrimXMLEvenementsServeurActes();
  
  $mbObject = new COperation();
  // Chargement de l'opération et génération du document
  if ($mbObject->load($object_id)) {
    $mbObject->loadRefs();
    foreach ($mbObject->_ref_actes_ccam as $acte_ccam) {
      $acte_ccam->loadRefsFwd();
    }
    $mbSejour =& $mbObject->_ref_sejour;
    $mbSejour->loadRefsFwd();
    $mbSejour->loadNumDossier();
    $mbSejour->_ref_patient->loadIPP();
  }
  
  if (!$evenementActivitePMSI->checkSchema()) {
    return;
  }
        
  $dest_hprim = new CDestinataireHprim();
  $dest_hprim->group_id = CGroups::loadCurrent()->_id;
  $dest_hprim->message = "pmsi";
  $destinataires = $dest_hprim->loadMatchingList();

  foreach ($destinataires as $_destinataire) {
    $evenementActivitePMSI->_ref_destinataire = $_destinataire;
    $msgEvtActivitePMSI = $evenementActivitePMSI->generateTypeEvenement($mbObject);

    $echange_hprim = $evenementActivitePMSI->_ref_echange_hprim;
    if ($doc_valid = $echange_hprim->message_valide) {
      $mbObject->facture = true;
      $mbObject->store();
    }
      
    if ($_destinataire->actif) {
      $source = CExchangeSource::get("$_destinataire->_guid-$evenementActivitePMSI->sous_type");
      if ($source->_id) {
        if ($doc_valid) {
          $source->setData($msgEvtActivitePMSI);
          if ($source->send()) {
            $echange_hprim->date_echange = mbDateTime();
            $echange_hprim->store();
          }
          $acquittement = $source->receive();
          if ($acquittement) {          
            $domGetAcquittement = new CHPrimXMLAcquittementsPMSI();
            $domGetAcquittement->loadXML(utf8_decode($acquittement));
            $domGetAcquittement->_ref_echange_hprim = $echange_hprim;       
            $doc_valid = $domGetAcquittement->schemaValidate();
            
            $echange_hprim->statut_acquittement = $domGetAcquittement->getStatutAcquittementServeurActivitePmsi();
            $echange_hprim->acquittement_valide = $doc_valid ? 1 : 0;
            $echange_hprim->_acquittement = $acquittement;
        
            $echange_hprim->store();
          }
        }
      }
    }
  }
}

if ($redirectUrl) {
  CAppUI::redirect($redirectUrl);
}

CApp::rip();

?>